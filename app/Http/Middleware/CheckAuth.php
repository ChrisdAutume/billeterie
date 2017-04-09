<?php

namespace App\Http\Middleware;

use Closure;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if(!$request->user()->hasRole($role))
        {
            $request->session()->flash('error',"Vous n'avez pas les droits nécessaire.");
            return redirect()->route('home');
        }
        return $next($request);
    }
}
