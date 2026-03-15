<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $customer = Customer::query()->where('email', (string) ($user->email ?? ''))->first();
        if (!$customer) {
            return view('shop.notifications.index', [
                'notifications' => collect(),
            ]);
        }

        $notifications = CustomerNotification::query()
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(15);

        return view('shop.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}
