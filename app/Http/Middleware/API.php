<?php
namespace App\Http\Middleware;

use Closure;
use App\Facility;

class API
{
    public function handle($request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
            'Access-Control-Allow-Origin' => "*"
        ];

        if ($request->getMethod() == "OPTIONS") {
            return Response::make("OK", 200, $headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }
        return $response;
    }
}