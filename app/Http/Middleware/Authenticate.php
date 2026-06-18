<?php namespace App\Http\Middleware;

use App\Cobalt\CobaltSession;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($this->auth->guest())
		{
            if ($request->hasCookie("vatusa-cobalt-token")) {
                $token = $request->cookie("vatusa-cobalt-token");
                $session = CobaltSession::fetchFromToken($token);
                if ($session !== null) {
                    \Auth::loginUsingId($session->user->cid, true);
                    return $next($request);
                }
            }
			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			else
			{
				return redirect()->guest('login');
			}
		}

		return $next($request);
	}

}
