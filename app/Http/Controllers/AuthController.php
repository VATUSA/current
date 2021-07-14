<?php

namespace App\Http\Controllers;

use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Classes\SMFHelper;
use App\Classes\VATUSAMoodle;
use App\Models\User;
use Auth;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getLogin()
    {
        if (env('APP_ENV', 'prod') == "dev" && !\Auth::check()) {
            /** In Development Environment */
            Auth::loginUsingId(env('DEV_CID_LOGIN', 0));
            /*$moodle = new VATUSAMoodle(true);
            $response = $moodle->request("auth_userkey_request_login_url",
                ['user' => ['idnumber' => \Illuminate\Support\Facades\Auth::user()->cid]]);
            $url = $response["loginurl"];

            return redirect($url . "&wantsurl=" . urlencode("https://www.vatusa.devel"));*/
        }
        if (!Auth::check()) {
            //If agreed on privacy policy, redirect to profile (opt in setting)
            //Otherwise, normal redirect to home
            $return = request()->has('agreed') ? "agreed" : env('LOGIN_ENV');

            return redirect()->guest(config('app.loginUrl') . "/?" . $return);
        } else {
            SMFHelper::setPermissions(Auth::user()->cid);
        }

        return redirect()->intended('/');
    }

}
