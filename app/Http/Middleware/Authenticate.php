<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate {

	protected $auth;

	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	public function handle($request, Closure $next)
	{
		if ($this->auth->guest())
		{
			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			if (config('cobalt.use_cobalt_login')) {
				return redirect(config('cobalt.login_url'));
			}
			return redirect()->guest('login');
		}

		return $next($request);
	}

}
