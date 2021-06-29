<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOnlyMiddleware
{
	protected $admins = [
		"Raymonf",
		"ben",
		"GameArcade",
		"succ",
		"EnergyCell",
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
		if (!Auth::check() || !in_array(Auth::user()->name, $this->admins))
		{
			return redirect('/');
		}

		return $next($request);
	}
}
