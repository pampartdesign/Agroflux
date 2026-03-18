<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileLocaleController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'locale' => ['required', Rule::in(array_keys(config('agroflux.locales')))],
        ]);

        $user = $request->user();
        $user->locale = $data['locale'];
        $user->save();

        // Also set session immediately for UX
        $request->session()->put('locale', $data['locale']);

        return back()->with('status', 'Language preference saved.');
    }
}
