<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Contracts\Encryption\Encrypter;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	protected $except_urls = [];

    /*public function __construct()
    {
        parent::__construct();
        $this->except_urls[] = env('AUTODEPLOY_ROUTE');
    }*/

	public function handle($request, Closure $next)
	{
        $regex = '#' . implode('|', $this->except_urls) . '#';
        if ($this->isReading($request) || $this->tokensMatch($request) || preg_match($regex, $request->path())) {
            return $next($request);
        } else {
            return parent::handle($request, $next);
        }
	}
}
