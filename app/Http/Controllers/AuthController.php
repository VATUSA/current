<?php

namespace App\Http\Controllers;

use App\Cobalt\CobaltAPIHelper;
use App\Cobalt\CobaltSession;
use App\Helpers\CookieHelper;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $cookieName = config('cobalt.cookie_name', 'vatusa-cobalt-token');
        // A browser can hold stale copies of the cobalt cookie under other
        // domains/paths; PHP's $request->cookie() returns only the first one
        // sent, which may be the stale one. Try every value until one decodes.
        $tokens = CookieHelper::allValues($request, $cookieName);
        $cid = null;
        $validToken = null;
        foreach ($tokens as $token) {
            $cid = CobaltSession::getCidFromToken($token);
            if ($cid !== null) {
                $validToken = $token;
                break;
            }
        }

        if ($cid === null) {
            Log::warning('Cobalt login callback failed', [
                'token_count' => count($tokens),
                'reason'      => count($tokens) ? 'no token decoded' : 'no token cookie',
            ]);
            return redirect('/')->with('error', 'Login failed, please try again.');
        }

        if (!Auth::loginUsingId($cid, true)) {
            // Cobalt authenticated them, but there's no `controllers` row to log in as.
            Log::warning('Cobalt login callback failed', [
                'cid'    => $cid,
                'reason' => 'no controllers row',
            ]);
            return redirect('/')->with('error', 'Your account could not be loaded. Please contact VATUSA Technical Support.');
        }
        $this->syncRatingFromCobalt($cid, $validToken);
        $response = redirect()->intended('/');

        if (count($tokens) > 1) {
            // Cobalt's real cookie is Domain=.vatusa.net; Path=/ (browsers treat
            // Domain=vatusa.net identically) - never expire that one.
            $stale = array_filter(CookieHelper::allVariants(), fn($v) => !($v[1] === '/' && in_array($v[0], ['.vatusa.net', 'vatusa.net'], true)));
            CookieHelper::expireVariants($response, $cookieName, $stale);
        }

        return $response;
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
