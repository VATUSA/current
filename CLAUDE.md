# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this
repository. For how this repo relates to the other VATUSA projects, see the workspace
`CLAUDE.md` one directory up.

## Project Overview

The main **VATUSA.net website** — a Laravel 12 / PHP application. It runs in parallel with
`current_api` (api.vatusa.net) and integrates with the newer cobalt API via `app/Cobalt`.

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

## Deployment

`build.sh` runs in the container: sets up `storage/`, runs `php artisan optimize`, and on
`prod`/`livedev`/`staging` runs migrations. `deploy.sh` builds assets, builds the
`vatusa/www` Docker image, and pushes to Docker Hub. Cluster deployment is managed in the
`gitops` repo.
