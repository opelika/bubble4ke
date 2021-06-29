<?php

namespace App\Http\Middleware;

use Auth;

use Carbon\Carbon;
use Closure;

class TimedMoneyGiverMiddleware
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
			// 86400 seconds in a day
			Auth::user()->last_visit = Carbon::now();
			if(strtotime(Auth::user()->last_money_collect_time) < \Carbon\Carbon::now()->timestamp - (86400)) {
				Auth::user()->money = Auth::user()->money + 10;
				Auth::user()->last_money_collect_time = Carbon::now();
			}
			Auth::user()->save();
		}
		return $next($request);
	}
}
