<?php namespace App\Http\Middleware;

use App\Cobalt\CobaltAPIHelper;
use App\Helpers\AuthHelper;
use Auth;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Cookie;

class AuthLastActivity
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->lastactivity = Carbon::now();
            $user->save();

            if (app()->environment("livedev") &&
                !AuthHelper::authACL()->isVATUSAStaff($user->cid) &&
                !AuthHelper::authACL()->isWebTeam() &&
                !in_array($user->cid, explode(',', config("app.livedev_cids", "")))) {
                \Auth::logout();

                return redirect("/")->with('error', 'You are not authorized to access the live development website.');
            }
            if (!$request->hasCookie("vatusa-cobalt-token")) {
                $token = CobaltAPIHelper::getCobaltUserToken($user->cid);
                if (strlen($token) > 0) {
                    Cookie::queue("vatusa-cobalt-token", $token, 60*24);
                }
                CobaltAPIHelper::syncRolesForUser($user);
            }
        }

        return $next($request);
    }

}
