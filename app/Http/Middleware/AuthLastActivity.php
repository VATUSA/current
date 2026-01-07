<?php namespace App\Http\Middleware;

use App\Classes\RoleHelper;
use App\Helpers\AuthHelper;
use Auth;
use Closure;
use Carbon\Carbon;

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
        }

        return $next($request);
    }

}
