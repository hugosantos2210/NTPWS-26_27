<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED_LOCALES = ['pt', 'en', 'hr'];

    /**
     * Handle an incoming request and set application locale from session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale');

        if (!$locale || !in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = config('app.locale', 'pt');
            if (!in_array($locale, self::SUPPORTED_LOCALES, true)) {
                $locale = 'pt';
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}

