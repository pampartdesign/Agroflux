<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next)
    {
        $available = array_keys(config('agroflux.locales', ['en' => 'English', 'el' => 'Greek']));

        // 1) Session
        $locale = $request->session()->get('locale');
        if (is_string($locale) && in_array($locale, $available, true)) {
            App::setLocale($locale);
            return $next($request);
        }

        // 2) Cookie (survives session expiry)
        $locale = $request->cookie('locale');
        if (is_string($locale) && in_array($locale, $available, true)) {
            App::setLocale($locale);
            return $next($request);
        }

        // 3) User DB locale (set when user explicitly switches language)
        $locale = $request->user()?->locale;
        if (is_string($locale) && in_array($locale, $available, true)) {
            App::setLocale($locale);
            return $next($request);
        }

        return $next($request);
    }
}
