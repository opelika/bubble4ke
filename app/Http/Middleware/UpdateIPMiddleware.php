<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Request;

use Illuminate\Support\Facades\Auth;

use Closure;

use Illuminate\Support\Facades\DB;

class UpdateIPMiddleware
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
		if(Auth::check()) {
			DB::table('users')->where('id', Auth::user()->id)->update(['last_ip' => Request::ip()]);
		}
		
		return $next($request);
	}
}
