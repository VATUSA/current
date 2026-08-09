<?php

namespace App\Http\Controllers;

use App\Cobalt\CobaltAPIHelper;
use App\Cobalt\CobaltSession;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller {

    public function __construct() {
        $this->middleware('guest', ['except' => 'callback']);
    }

    public function getLogin(Request $request) {
        // Dev auto-login when no cobalt token is present
        if (config('app.env', 'prod') == "dev" && !\Auth::check()) {
            Auth::loginUsingId(config('app.dev_cid_login', 0), true);
        }
        if (!Auth::check()) {
            if (config('cobalt.use_cobalt_login')) {
                $callback = rtrim(config('app.url'), '/') . '/auth/callback';
                return redirect(rtrim(config('cobalt.login_url'), '/') . '?redirect=' . urlencode($callback));
            }
            $return = request()->has('agreed') ? "agreed" : config('app.login_env');
            return redirect()->guest(config('app.loginUrl') . "/?" . $return);
        }
        return redirect()->intended('/');
    }

    /**
     * Entry point cobalt redirects back to after a successful login. Reads
     * the cobalt JWT cookie once, converts it into an independent Laravel
     * session, and never touches the cobalt cookie again after this.
     */
    public function callback(Request $request) {
        $token = $request->cookie(config('cobalt.cookie_name', 'vatusa-cobalt-token'));
        if ($token) {
            $cid = CobaltSession::getCidFromToken($token);
            if ($cid !== null) {
                Auth::loginUsingId($cid, true);
                $this->syncRatingFromCobalt($cid, $token);
            }
        }
        return redirect()->intended('/');
    }

    /**
     * Cobalt is the source of truth for VATSIM network rating, but current's
     * own `controllers.rating` is only ever provisioned once (INSERT IGNORE)
     * by cobalt, never updated after that. Pull the live rating from cobalt
     * on every login so reinstatements/suspensions/promotions take effect
     * the next time the controller logs in, without waiting on a cron job.
     */
    private function syncRatingFromCobalt(int $cid, string $token): void {
        $json = CobaltAPIHelper::getUserSessionFromToken($token);
        $rating = $json['user']['network_user']['rating'] ?? null;
        if ($rating === null) {
            return;
        }
        User::where('cid', $cid)->update(['rating' => $rating]);
    }

}
