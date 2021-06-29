<?php
namespace App\Http\Middleware;
use Closure;
use \Illuminate\Support\Facades\Redirect;

class LowercaseRoutesMiddleware
{
	protected $excluded_strings = [
		"client/",
		"server/",
		"Asset/",
	];

	/**
	 * Run the request filter.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		foreach($this->excluded_strings as $excluded) {
			if (strpos($request->path(), $excluded) !== false) {
				return $next($request);
			}
		}

		if (!ctype_lower(preg_replace('/[^A-Za-z]/', '', $request->path())) && $request->path() != "/") {
			$new_route = str_replace($request->path(), strtolower($request->path()), $request->fullUrl());
			//echo "New route: " . $new_route . " / Req path: " . $request->path();
			return Redirect::to($new_route, 301);
		}

		return $next($request);
	}
}