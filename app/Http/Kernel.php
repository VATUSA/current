<?php namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Fruitcake\Cors\HandleCors::class,
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
//        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'smf'           => 'App\Http\Middleware\SMFCheck',
        'auth'          => 'App\Http\Middleware\Authenticate',
        'auth.basic'    => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'bindings'      => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'           => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'         => 'App\Http\Middleware\RedirectIfAuthenticated',
        'ins'           => 'App\Http\Middleware\INS',
        'facilitysr'    => 'App\Http\Middleware\FacilitySrStaff',
        'vatusastaff'   => 'App\Http\Middleware\VATUSAStaff',
        'apikey'        => 'App\Http\Middleware\APIKey',
        'api'           => 'App\Http\Middleware\API',
        "csrf"          => 'App\Http\Middleware\VerifyCsrfToken',
        'lastactivity'  => 'App\Http\Middleware\AuthLastActivity',
        'privacy-agree' => 'App\Http\Middleware\PrivacyAgree',
    ];

}
