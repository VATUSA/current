<?php

namespace App\Http\Controllers;

use App\Classes\SMFHelper;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller {

    public function __construct() {
        $this->middleware('guest');
    }

    public function getLogin(Request $request) {
        // Dev auto-login when no cobalt token is present
        if (config('app.env', 'prod') == "dev" && !\Auth::check()) {
            Auth::loginUsingId(config('app.dev_cid_login', 0), true);
        }
        if (!Auth::check()) {
            $return = request()->has('agreed') ? "agreed" : config('app.login_env');
            if (config('cobalt.use_cobalt_login')) {
                return redirect(rtrim(config('cobalt.login_url'), '/') . '/?' . $return);
            }
            return redirect()->guest(config('app.loginUrl') . "/?" . $return);
        }
        SMFHelper::setPermissions(Auth::user()->cid);
        return redirect()->intended('/');
    }

}
