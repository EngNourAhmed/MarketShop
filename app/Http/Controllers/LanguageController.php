<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function set(Request $request)
    {
        $lang = (string) $request->input('lang', 'en');
        if (!in_array($lang, ['en', 'ar'], true)) {
            $lang = 'en';
        }

        $request->session()->put('lang', $lang);

        return back();
    }
}
