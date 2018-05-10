<?php
namespace App\Http\Middleware;

use Closure;
use App\Facility;

class APIKey
{
    public function handle($request, Closure $next)
    {
        $apikey = $request->apikey;
        $ip = $request->ip();

        if (Facility::where('apikey',$apikey)->where('ip',$ip)->count() < 1 && Facility::where('api_sandbox_key', $apikey)->where('api_sandbox_ip', $ip)->count() < 1) {
            \Log::warning("API Unauthorized request from $apikey and $ip");
            return response()->json([
                'error' => 401,
                'message' => "Unauthorized",
                'ip' => $ip
            ], 401);
        }

        if (Facility::where('api_sandbox_key', $apikey)->where('api_sandbox_ip', $ip)->count() >= 1) {
            // Sandbox, force test flag..
            $request->merge(['test' => 1]);
            $facility = Facility::where('api_sandbox_key', $apikey)->where('api_sandbox_ip', $ip)->first();
        } else {
            $facility = Facility::where('apikey', $apikey)->where('ip', $ip)->first();
        }

        $data = file_get_contents("php://input");
        $data .= var_export($_POST, true);

        \DB::table("api_log")->insert(
            ['facility' => $facility->id,
             'datetime' => \DB::raw('NOW()'),
             'method' => $request->method(),
             'url' => $request->fullUrl(),
             'data' => ($request->has('test') ? "SANDBOX: " : "LIVE: ") . $data]);

        return $next($request);
    }
}