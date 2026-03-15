<?php

namespace App\Helpers;

use App\Models\Countery;
use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    /**
     * Get the current selected country from session.
     * Default to Egypt.
     */
    public static function getCurrentCountry()
    {
        $countryId = Session::get('country_id');
        
        if ($countryId) {
            return Countery::find($countryId) ?? Countery::where('code', 'EG')->first();
        }

        return Countery::where('code', 'EG')->first();
    }

    /**
     * Convert and format a price from EGP to the current country's currency.
     */
    public static function format($price)
    {
        $country = self::getCurrentCountry();
        if (!$country) return number_format($price, 2) . ' EGP';

        $convertedPrice = $price / $country->exchange_rate;

        // Custom formatting logic
        if ($convertedPrice >= 1000) {
            $formatted = round($convertedPrice / 1000) . 'k';
        } else {
            $formatted = number_format($convertedPrice, $convertedPrice < 1 ? 2 : 0);
        }

        $isAr = session('lang') === 'ar';
        $symbol = $isAr ? $country->currency_symbol : $country->currency_code;

        return $isAr ? $formatted . ' ' . $symbol : $symbol . ' ' . $formatted;
    }

    /**
     * Get only the converted value.
     */
    public static function convert($price)
    {
        $country = self::getCurrentCountry();
        if (!$country) return $price;

        return $price / $country->exchange_rate;
    }

    /**
     * Get the currency symbol.
     */
    public static function getSymbol()
    {
        $country = self::getCurrentCountry();
        if (!$country) return session('lang') === 'ar' ? 'ج.م' : 'EGP';
        
        return session('lang') === 'ar' ? $country->currency_symbol : $country->currency_code;
    }

    /**
     * Resolve a product/category image URL.
     * If the stored value is an external URL (http/https), return as-is.
     * Otherwise, return asset('storage/path').
     */
    public static function imageUrl(?string $path, ?string $fallback = null): string
    {
        if (empty($path)) {
            return $fallback ?? asset('apple-touch-icon.png');
        }

        // Already a full external URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . $path);
    }
}
