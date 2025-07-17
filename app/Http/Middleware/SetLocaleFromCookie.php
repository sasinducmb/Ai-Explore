<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SetLocaleFromCookie
{
    public function handle($request, Closure $next)
    {
        $locale = Cookie::get('locale', config('app.locale'));

        if (in_array($locale, ['en', 'si', 'ta'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
