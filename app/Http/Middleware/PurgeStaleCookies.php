<?php namespace App\Http\Middleware;

use App\Helpers\CookieHelper;
use Closure;

/**
 * Expires the pre-rename legacy session cookie (VATUSA_token) under every
 * domain/path it may have been issued with. Stale copies of it were shadowing
 * the real session cookie and preventing logins. Safe to remove once those
 * old cookies have aged out of browsers.
 */
class PurgeStaleCookies
{
    const STALE_COOKIES = ['VATUSA_token'];

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        foreach (self::STALE_COOKIES as $name) {
            if ($name !== config('session.cookie') && CookieHelper::allValues($request, $name)) {
                CookieHelper::expireVariants($response, $name, CookieHelper::allVariants());
            }
        }

        return $response;
    }
}
