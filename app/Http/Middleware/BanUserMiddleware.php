<?php

namespace App\Http\Middleware;

use App\IpBans;
use Closure;
use DB;
use Auth;

class BanUserMiddleware
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
			$banned = DB::table('users')->where('id', \Auth::user()->id)->first()->banned;
			if($banned == 1) {
				return response()->view('auth.banned');
			}

			if(IpBans::where('ip', $request->ip())->first()) {
				return response()->view('auth.banned');
			}
		}
		return $next($request);
	}
}
