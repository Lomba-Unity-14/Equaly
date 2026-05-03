# AGENTS.md

## Commands

```bash
# First-time setup (installs PHP + JS deps, generates key, migrates, builds assets)
composer run setup

# Dev server (runs serve + queue + pail + vite concurrently)
composer run dev

# Run tests (clears config first, then php artisan test)
composer run test

# Single test
php artisan test --filter=TestName

# Build frontend assets
npm run build

# Code style (Laravel Pint)
./vendor/bin/pint
```

## Architecture

- **Laravel 13** on PHP 8.3+ with **SQLite** as the default database
- **Livewire 4** is installed for full-stack reactivity
- **Tailwind CSS v4** via Vite (CSS-first config, no `tailwind.config.js`)
- Session and queue both use the `database` driver — migrations must be run for dev
- Test database is SQLite `:memory:` — no separate test DB setup needed
- Health endpoint: `/up`

## Conventions

- **Model attributes use PHP attributes** (`#[Fillable]`, `#[Hidden]`), not `$fillable`/`$hidden` properties — this is a Laravel 13 convention
- **Tailwind v4** config lives in `resources/css/app.css` under `@theme`, not in a JS config file
- **Vite input** key is `input` (singular) — Laravel Vite Plugin v3
- **`.npmrc`** sets `ignore-scripts=true` — npm lifecycle scripts don't auto-run
- **Laravel Pint** is the formatter, not php-cs-fixer
