<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;


class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Gate::allows('admin')) {
            return $next($request);
        }

        return redirect(config('mediactive-digital.medkit.redirect_if_not_admin'));
    }
}
