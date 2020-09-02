<?php namespace App\Http\Middleware;

use App\Classes\RoleHelper;
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

            if (app()->environment("livedev") && !RoleHelper::isVATUSAStaff($user->cid) && !in_array($user->cid,
                    explode(',', env("LIVEDEV_CIDS", "")))) {
                return redirect()->to('/logout');
            }
        }

        return $next($request);
    }

}
