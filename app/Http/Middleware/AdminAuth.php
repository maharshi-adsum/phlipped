<?php

namespace App\Http\Middleware;

use Closure;
use Session;
class AdminAuth
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
        $path = $request->path();
        if(($path=="login") && Session::get('user') || ($path == "admin"))
        {
            return redirect('/index');
        }
        else if(($path != "login" && !Session::get('user')))
        {
            return redirect('/login');
        }
        return $next($request);
    }
}
