<?php

namespace App\Http\Controllers;

use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Classes\SMFHelper;
use App\User;
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

            return redirect()->intended('/');
        }
        if (!Auth::check()) {
            //If agreed on privacy policy, redirect to profile (opt in setting)
            //Otherwise, normal redirect to home
            $return = request()->has('agreed') ? "agreed" : env('LOGIN_ENV');

            if(app()->environment('staging')) {
                return redirect()->guest('https://login.staging.vatusa.net/?'.$return);
            }

            return redirect()->guest(app()->environment("livedev") ? 'https://login.dev.vatusa.net/?' . $return : 'https://login.vatusa.net/?' . $return);
        } else {
            SMFHelper::setPermissions(Auth::user()->cid);
        }

        return redirect()->intended('/');
    }

}
