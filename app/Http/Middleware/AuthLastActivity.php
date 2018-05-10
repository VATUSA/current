<?php namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class AuthLastActivity {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
	    if (\Auth::check()) {
	        $user = \Auth::user();
            $user->lastactivity = Carbon::now();
            $user->save();
        }

		return $next($request);
	}

}
