<?php
/**
 * Cloudflare IPs Service Provider
 * Convert Cloudflare IPs to real IPs
 * @author Blake Nahin <vatusa12@vatusa.net>
 */

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\IpUtils;

class CloudflareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (IpUtils::checkIp(request()->ip(), $this->fetchIps())) {
            request()->server->add([
                'ORIGINAL_REMOTE_ADDR' => request()->ip(),
                'REMOTE_ADDR'          => filter_var(request()->header('HTTP_CF_CONNECTING_IP'),
                    FILTER_VALIDATE_IP) ?: request()->ip()
            ]);
        }
    }

    protected function fetchIps()
    {
        $ips = Cache::get('cf_ips');
        if ($ips) {
            return $ips;
        }

        $guzzle = new Client();
        $return = $guzzle->get("https://api.cloudflare.com/client/v4/ips");
        if ($return->getStatusCode() == 200) {
            $list = json_decode($return->getBody(), true);
            $ips = array_merge($list["result"]["ipv4_cidrs"], $list["result"]["ipv6_cidrs"]);
            Cache::put('cf_ips', $ips, 60 * 60 * 24);

            return $ips;
        } else {
            return [];
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
