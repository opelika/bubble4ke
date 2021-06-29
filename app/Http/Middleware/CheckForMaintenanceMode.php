<?php

namespace App\Http\Middleware;

use Closure;

class CheckForMaintenanceMode
{
	protected $excluded_ips = [
		"127.0.0.1",
	];

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (app()->isDownForMaintenance() && !in_array($request->ip(), $this->excluded_ips))
		{
			return response('appels made it work on Windows XP, so maintenance time (not another dmca) until I am motivated to redo the client to not work on XP. <a href="//forum.rblxdev.pw">forums are here</a>', 503);
		}
		return $next($request);
	}
}
