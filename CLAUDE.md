# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this
repository. For how this repo relates to the other VATUSA projects, see the workspace
`CLAUDE.md` one directory up.

## Project Overview

The main **VATUSA.net website** — a Laravel 12 / PHP application. It runs in parallel with
`api` (api.vatusa.net).

## Build & Development Commands

```sh
composer install        # PHP dependencies
npm install             # JS dependencies

npm run dev             # Development webpack build (Laravel Mix)
npm run watch           # Rebuild on change
npm run prod            # Production build

php artisan migrate     # Run DB migrations
php artisan optimize    # Cache config/routes (run in container build)
php artisan tinker      # REPL

./vendor/bin/phpunit    # Run tests (PHPUnit 9, suite in ./tests)
```

Frontend assets are bundled with **Laravel Mix / webpack** (not Vite). Auth uses Laravel
Socialite with the Discord provider.

## Architecture

Standard Laravel layout under `app/`:
- `Http/` — controllers, middleware
- `Models/` — Eloquent models
- `Services/`, `Classes/`, `Helpers/` — business logic
- `Cobalt/` — integration layer for the cobalt API
- `Console/`, `Commands/` — artisan commands and scheduled tasks
- `Events/`, `Handlers/`, `Providers/` — Laravel event/service wiring

## Authentication (Cobalt login flow)

`current` does not implement its own OAuth client. `cobalt` (cobalt.vatusa.net /
cobalt.vatusa.dev) is the identity provider — it owns the VATSIM Connect OAuth registration
(VATSIM only whitelists a `cobalt.vatusa.net` redirect_uri) and issues a self-contained HS256
JWT in a `vatusa-cobalt-token` cookie after a successful login.

`current` is **decoupled** from cobalt after login — it does not maintain an ongoing
dependency on the cobalt cookie or session. The integration is a one-time handoff:

1. `GET /login` (`AuthController@getLogin`, and equivalently the `Authenticate` middleware's
   guest redirect) sends the browser to cobalt's `/login`, passing a `redirect` query param
   pointing back at `current`'s own `{APP_URL}/auth/callback`.
2. Cobalt completes the VATSIM Connect OAuth flow (proxying through prod cobalt for
   hosted dev/staging environments, since VATSIM's redirect_uri is prod-only), sets its own
   session cookie, then redirects to the `redirect` target it was given — validated against
   cobalt's `REDIRECT_ALLOWLIST` to prevent open redirects.
3. `GET /auth/callback` (`AuthController@callback`) runs **once**: reads the cobalt cookie,
   decodes the JWT locally via `App\Cobalt\CobaltSession::getCidFromToken()` (shared
   `JWT_KEY`, with an HTTP fallback to cobalt's `/tokenSession` endpoint if local decode
   fails), and calls `Auth::loginUsingId($cid, true)` to establish `current`'s own
   independent Laravel session. From this point on `current` never re-reads the cobalt
   cookie.

This replaced an earlier design (PR #169) that re-checked the cobalt cookie on **every**
request via a global `CobaltAuthBridge` middleware — that middleware and its Kernel
registration have been removed. The current design is intentionally a one-way, one-time
handoff: logging out of `current` (`/logout`) only clears `current`'s own session and does
not touch the cobalt cookie or log the user out of the newer stack (`webapps`), and vice
versa. This is deliberate — `current` is being fully retired once migration completes, so
ongoing cross-app SSO was judged not worth the coupling.

Config keys: `cobalt.use_cobalt_login` (bool, gates whether `/login` redirects to cobalt vs.
the legacy pre-cobalt flow), `cobalt.login_url`, `cobalt.cookie_name`. See
`gitops/current/overlays/*/www-configmap.yaml` for `COBALT_LOGIN_URL` /
`USE_COBALT_LOGIN` per environment.

The cobalt side of this flow (staging login relay, `redirect`/`state` param threading, open
redirect allowlist) is documented in `cobalt`'s own `CLAUDE.md` / commit history — see
`cobalt/src/endpoints/login.go` (`GetLogin`, `GetLoginForStaging`, `Connect`,
`LoginUseToken`).

## Deployment

`build.sh` runs in the container: sets up `storage/`, runs `php artisan optimize`, and on
`prod`/`livedev`/`staging` runs migrations. `deploy.sh` builds assets, builds the
`vatusa/www` Docker image, and pushes to Docker Hub. Cluster deployment is managed in the
`gitops` repo.
