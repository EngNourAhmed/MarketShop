<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResponse;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Customer;
use Illuminate\Support\Str;

class cardGenerationController extends Controller
{
    private function generateUniqueCardNumber(): string
    {
        do {
            $candidate = 'TRD' . Str::upper(Str::random(10));
        } while (Card::where('card_number', $candidate)->exists());

        return $candidate;
    }

    private function getPointPricing(Card $card): array
    {
        $currency = 'eg';
        $pointPrice = (float) ($card->price_in_eg ?? 0);

        if ((float) ($card->price_in_us ?? 0) > 0) {
            $currency = 'us';
            $pointPrice = (float) $card->price_in_us;
        }

        if ((float) ($card->price_in_uk ?? 0) > 0) {
            $currency = 'uk';
            $pointPrice = (float) $card->price_in_uk;
        }

        return [
            'currency' => $currency,
            'point_price' => $pointPrice,
        ];
    }

    public function index(Request $request)
    {
        $cards = Card::with('customer')->latest()->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(CardResponse::collection($cards));
        }

        $totalCards = $cards->count();
        $totalPoints = (int) $cards->sum('points');
        $totalAmount = (float) $cards->sum('amount');

        $customers = Customer::orderBy('name')->get();

        return view('card.index', [
            'cards' => $cards,
            'customers' => $customers,
            'totalCards' => $totalCards,
            'totalPoints' => $totalPoints,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function store(Request $request)
    {
        $isApiRequest = $request->expectsJson() || $request->is('api/*');

        $rules = [
            'card_number' => 'nullable|string|unique:cards,card_number',
            'card_type' => 'required|string',
            'card_holder' => 'required|string',
            'cvv' => 'required|string',
            'expiry_date' => 'required|string',
            'type' => 'required|string',
            'status' => 'nullable|string',
            'distribution' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'points' => $isApiRequest ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'currency' => $isApiRequest ? 'nullable|in:eg,us,uk' : 'required|in:eg,us,uk',
            'point_price' => $isApiRequest ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
        ];

        if ($isApiRequest) {
            $rules['balance'] = 'nullable|numeric|min:0';
            $rules['amount'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $cardNumber = $validated['card_number'] ?? $this->generateUniqueCardNumber();

        $points = (int) ($validated['points'] ?? 0);
        $currency = $validated['currency'] ?? 'eg';
        $pointPrice = array_key_exists('point_price', $validated) ? (float) $validated['point_price'] : null;

        $priceInEg = 0;
        $priceInUs = 0;
        $priceInUk = 0;

        if ($pointPrice !== null) {
            $priceInEg = $currency === 'eg' ? $pointPrice : 0;
            $priceInUs = $currency === 'us' ? $pointPrice : 0;
            $priceInUk = $currency === 'uk' ? $pointPrice : 0;
        }

        if ($pointPrice !== null) {
            $amount = round($points * $pointPrice, 2);
            $balance = $amount;
        } else {
            $amount = array_key_exists('amount', $validated) ? (float) ($validated['amount'] ?? 0) : 0;
            $balance = array_key_exists('balance', $validated) ? (float) ($validated['balance'] ?? $amount) : $amount;
        }

        $card = new Card();
        $card->forceFill([
            'card_number' => $cardNumber,
            'card_type' => $validated['card_type'],
            'card_holder' => $validated['card_holder'],
            'cvv' => $validated['cvv'],
            'expiry_date' => $validated['expiry_date'],
            'type' => $validated['type'],
            'status' => $validated['status'] ?? 'active',
            'distribution' => $validated['distribution'] ?? null,
            'customer_id' => $validated['customer_id'] ?? null,
            'points' => $points,
            'points_remaining' => $points,
            'amount' => $amount,
            'balance' => $balance,
            'price_in_eg' => $priceInEg,
            'price_in_us' => $priceInUs,
            'price_in_uk' => $priceInUk,
        ]);
        $card->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($card, 201);
        }

        return redirect()->route('cards.index')->with('status', 'تم إضافة الكارت بنجاح');
    }

    public function show($id)
    {
        $card = Card::with('customer')->findOrFail($id);
        return response()->json($card);
    }

    public function update(Request $request, $id)
    {
        $card = Card::findOrFail($id);

        $isApiRequest = $request->expectsJson() || $request->is('api/*');

        $rules = [
            'card_number' => 'sometimes|required|string|unique:cards,card_number,' . $id,
            'card_type' => 'sometimes|required|string',
            'card_holder' => 'sometimes|required|string',
            'cvv' => 'sometimes|required|string',
            'expiry_date' => 'sometimes|required|string',
            'type' => 'sometimes|required|string',
            'status' => 'nullable|string',
            'distribution' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'points' => $isApiRequest ? 'nullable|integer|min:0' : 'sometimes|required|integer|min:0',
            'currency' => $isApiRequest ? 'nullable|in:eg,us,uk' : 'sometimes|required|in:eg,us,uk',
            'point_price' => $isApiRequest ? 'nullable|numeric|min:0' : 'sometimes|required|numeric|min:0',
        ];

        if ($isApiRequest) {
            $rules['balance'] = 'nullable|numeric|min:0';
            $rules['amount'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        $currentPricing = $this->getPointPricing($card);
        $currency = $validated['currency'] ?? $currentPricing['currency'];
        $pointPrice = array_key_exists('point_price', $validated) ? (float) $validated['point_price'] : (float) $currentPricing['point_price'];
        $points = array_key_exists('points', $validated) ? (int) $validated['points'] : (int) ($card->points ?? 0);

        $hasNewPointPrice = array_key_exists('point_price', $validated) || array_key_exists('currency', $validated) || array_key_exists('points', $validated);

        $priceInEg = $currency === 'eg' ? $pointPrice : 0;
        $priceInUs = $currency === 'us' ? $pointPrice : 0;
        $priceInUk = $currency === 'uk' ? $pointPrice : 0;

        if ($hasNewPointPrice) {
            $amount = round($points * $pointPrice, 2);
            $balance = $amount;
        } else {
            $amount = array_key_exists('amount', $validated) ? (float) ($validated['amount'] ?? $card->amount) : (float) ($card->amount ?? 0);
            $balance = array_key_exists('balance', $validated) ? (float) ($validated['balance'] ?? $card->balance) : (float) ($card->balance ?? 0);
        }

        $card->forceFill(collect($validated)->only([
            'card_number',
            'card_type',
            'card_holder',
            'cvv',
            'expiry_date',
            'type',
            'status',
            'distribution',
            'customer_id',
        ])->toArray());

        $card->forceFill([
            'points' => $points,
            'amount' => $amount,
            'balance' => $balance,
            'price_in_eg' => $priceInEg,
            'price_in_us' => $priceInUs,
            'price_in_uk' => $priceInUk,
        ]);
        $card->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($card);
        }

        return redirect()->route('cards.index')->with('status', 'تم تعديل الكارت بنجاح');
    }

    public function destroy($id)
    {
        $card = Card::findOrFail($id);
        $card->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Card deleted successfully']);
        }

        return redirect()->route('cards.index')->with('status', 'تم حذف الكارت بنجاح');
    }
}
