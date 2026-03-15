<?php

namespace App\Http\Controllers;

use App\Models\AdvertiserAgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdvertiserAgencyController extends Controller
{
    /**
     * عرض كل الوكالات
     */
    public function index(Request $request)
    {
        $agencies = AdvertiserAgency::latest()->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $agencies,
            ]);
        }

        $totalCost = (float) $agencies->sum('cost');

        return view('advertisement.index', [
            'agencies' => $agencies,
            'totalCost' => $totalCost,
        ]);
    }

    /**
     * إنشاء وكالة جديدة
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:advertiser_agencies,email'],
            'website' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive,pending,blocked',
            'user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'logo' => 'nullable|string|max:255',
        ]);

        if (empty($data['user_id'])) {
            $data['user_id'] = Auth::id();
        }

        $agency = AdvertiserAgency::create($data);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'تم إنشاء وكالة الإعلان بنجاح',
                'data' => $agency
            ], 201);
        }

        return redirect()->route('advertisement.index')->with('status', 'تم إنشاء وكالة الإعلان بنجاح');
    }

    /**
     * عرض وكالة واحدة
     */
    public function show($id)
    {
        return response()->json(
            AdvertiserAgency::findOrFail($id)
        );
    }

    /**
     * تحديث وكالة
     */
    public function update(Request $request, $id)
    {
        $agency = AdvertiserAgency::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => [
                'required',
                'email:rfc',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
                Rule::unique('advertiser_agencies', 'email')->ignore($agency->id),
            ],
            'website' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive,pending,blocked',
            'user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'logo' => 'nullable|string|max:255',
        ]);

        if (empty($data['user_id'])) {
            $data['user_id'] = $agency->user_id ?? Auth::id();
        }

        $agency->update($data);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'تم تحديث وكالة الإعلان بنجاح',
                'data' => $agency
            ]);
        }

        return redirect()->route('advertisement.index')->with('status', 'تم تحديث وكالة الإعلان بنجاح');
    }

    /**
     * حذف وكالة
     */
    public function destroy($id)
    {
        $agency = AdvertiserAgency::findOrFail($id);
        $agency->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'message' => 'تم حذف وكالة الإعلان بنجاح'
            ]);
        }

        return redirect()->route('advertisement.index')->with('status', 'تم حذف وكالة الإعلان بنجاح');
    }
}
