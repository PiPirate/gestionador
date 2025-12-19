<?php

namespace App\Http\Controllers;

use App\Support\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'currency' => 'required|in:usd,cop',
        ]);

        Currency::switch($data['currency']);

        return back();
    }
}
