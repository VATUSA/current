<?php namespace App\Http\Middleware;

use App\Cobalt\CobaltSession;
use Closure;
use Illuminate\Http\Request;

class CobaltAuthBridge
{
    /**
     * On every request, if Laravel has no session, check for a cobalt JWT cookie
     * and log the user in from it. This runs globally so that any page load
     * (not just auth-guarded routes) picks up an active cobalt session.
     */
    public function handle(Request $request, Closure $next)
    {
        if (\Auth::guest()) {
            $cookieName = config('cobalt.cookie_name', 'vatusa-cobalt-token');
            if ($request->hasCookie($cookieName)) {
                $cid = CobaltSession::getCidFromToken($request->cookie($cookieName));
                if ($cid !== null) {
                    \Auth::loginUsingId($cid, true);
                }
            }
        }
        return $next($request);
    }
}
