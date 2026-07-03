<?php

namespace App\Http\Controllers;

use App\Classes\SMFHelper;
use App\Cobalt\CobaltSession;
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
        SMFHelper::setPermissions(Auth::user()->cid);
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
                SMFHelper::setPermissions($cid);
            }
        }
        return redirect()->intended('/');
    }

}
