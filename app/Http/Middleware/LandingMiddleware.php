<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http;

class LandingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */

    protected $except = [
        'login/*',
        'login',
        'dev/login/*',
    ];

    public function handle($request, Closure $next)
    {
        $date_end = config('billeterie.landing_until', null);

        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if (!is_null($date_end) && (new \DateTime() < $date_end) && !$this->inExceptArray($request)) {
                return new Http\Response(view('landing.index'));
            }
        }

        return $next($request);
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
