<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Helpers for cookies that can exist more than once under the same name.
 *
 * Browsers send every cookie whose domain/path matches the request, so a
 * stale copy (e.g. host-only www.vatusa.net, or path /legacy) can coexist
 * with the current one. PHP keeps only the first value in $_COOKIE, which
 * is the stale one, so these helpers work from the raw Cookie header.
 */
class CookieHelper
{
    /** Domains we may have issued cookies under in the past; null = host-only. */
    public const DOMAINS = [null, 'www.vatusa.net', '.vatusa.net', 'vatusa.net'];

    /** Paths we may have issued cookies under in the past. */
    public const PATHS = ['/', '/legacy', '/legacy/'];

    /**
     * Every value sent for $name in the raw Cookie header, in the order sent.
     *
     * @return string[]
     */
    public static function allValues(Request $request, string $name): array
    {
        $values = [];
        foreach (explode(';', (string) $request->headers->get('Cookie', '')) as $pair) {
            $parts = explode('=', trim($pair), 2);
            if (count($parts) === 2 && $parts[0] === $name && $parts[1] !== '') {
                $values[] = urldecode($parts[1]);
            }
        }
        return $values;
    }

    /**
     * Add expiring Set-Cookie headers for $name under each domain/path pair.
     * Uses the response header bag directly because Cookie::queue keys only
     * by name+path, so domain variants on the same path would collapse.
     *
     * @param array<array{0: ?string, 1: string}> $variants [domain, path] pairs
     */
    public static function expireVariants(Response $response, string $name, array $variants): void
    {
        foreach ($variants as [$domain, $path]) {
            $response->headers->setCookie(
                Cookie::create($name, null, 1, $path, $domain, true, true, false, Cookie::SAMESITE_NONE)
            );
        }
    }

    /**
     * Every [domain, path] pair in DOMAINS x PATHS.
     *
     * @return array<array{0: ?string, 1: string}>
     */
    public static function allVariants(): array
    {
        $variants = [];
        foreach (self::DOMAINS as $domain) {
            foreach (self::PATHS as $path) {
                $variants[] = [$domain, $path];
            }
        }
        return $variants;
    }
}
