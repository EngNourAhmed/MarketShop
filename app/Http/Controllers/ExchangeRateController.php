<?php

namespace App\Http\Controllers;

use App\Models\Countery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ExchangeRateController extends Controller
{
    /**
     * Set the current country for the shop.
     */
    public function setShopCountry(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:counteries,id',
        ]);

        Session::put('country_id', $request->country_id);

        return back()->with('status', __('Country updated successfully.'));
    }
}
