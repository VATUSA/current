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
     * @return Response
     */
    public function getLogin()
    {
        if (!\Auth::check()) {
            return redirect()->guest('https://login.vatusa.net/?' . env('LOGIN_ENV'));
        } else {

                SMFHelper::setPermissions(\Auth::user()->cid);

        }

        return redirect()->intended('/');
    }

}
