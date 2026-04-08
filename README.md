# README

An opinionated, coding agent-ready, Laravel 13 application skeleton preconfigured with strict code quality tooling, Octane-ready conventions, and a comprehensive testing pipeline. Use this as a starting point for new Laravel services.

## What's Included

Out of the box, the skeleton provides:

- **Laravel Octane** with FrankenPHP worker — high-performance, long-running process model
- **Custom ULID primary keys** — `App\Support\Ulids\HasUlids` trait and `UlidCast`, enforced over Laravel's built-in implementation
- **Brick\DateTime integration** — `LocalDateCast` and `LocalDateTimeCast` for Eloquent, replacing Carbon entirely
- **Five-layer testing pipeline** — PHPUnit, Behat BDD, PHPStan (level max), Infection mutation testing, Mago linting
- **100% coverage and mutation score** — enforced automatically; builds fail if either drops
- **Strict linting** — final controllers, identity comparison, strict types, no eval/shell/globals
- **Concurrent dev server** — single command runs Octane, queue worker, log tailing, and Vite

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Language | PHP | 8.5 |
| Framework | Laravel | 13 |
| App Server | Laravel Octane | 2 |
| Database | SQLite (dev) | - |
| Frontend | Tailwind CSS | 4 |
| Build Tool | Vite | 8 |
| Date/Time | Brick\DateTime | 0.9 |
| IDs | Custom ULIDs | - |

## Getting Started

### Prerequisites

- PHP 8.5+
- Composer
- Node.js and npm
- SQLite

### Setup

```bash
composer setup
```

This runs the full setup sequence: installs PHP and Node dependencies, generates an app key, runs migrations, and builds frontend assets.

### Development Server

```bash
composer run dev
```

Starts four processes concurrently:

- **server** - `php artisan serve`
- **queue** - `php artisan queue:listen`
- **logs** - `php artisan pail` (log tailing)
- **vite** - `npm run dev` (hot-reload)

## Project Structure

```
app/
  Models/                    Eloquent models (ULID primary keys)
  Support/
    DateTime/                LocalDateCast, LocalDateTimeCast (Brick\DateTime)
    Ulids/                   Custom HasUlids trait and UlidCast
config/                      Laravel configuration (including octane.php)
database/
  factories/                 Model factories
  migrations/                Database migrations
  seeders/                   Database seeders
features/                    Behat BDD feature files (Gherkin)
resources/
  css/                       Tailwind CSS v4 entrypoint
  js/                        JavaScript entrypoint (Axios)
  views/                     Blade templates
routes/
  web.php                    Web routes
  console.php                Console commands
tests/
  Behat/FeatureContext.php   Behat step definitions
  Feature/                   PHPUnit feature tests
  Unit/                      PHPUnit unit tests
```

## Conventions

### No Carbon or DateTime

All date/time values use `Brick\DateTime\LocalDate` or `Brick\DateTime\LocalDateTime`. Carbon and PHP's `DateTime` are banned via PHPStan disallowed-namespace rules. Custom Eloquent casts (`LocalDateCast`, `LocalDateTimeCast`) handle serialisation.

### Custom ULIDs

Use `App\Support\Ulids\HasUlids` instead of Laravel's built-in `HasUlids` trait. This is enforced by PHPStan. The custom implementation includes a `UlidCast` for Eloquent attribute casting.

### Octane-Safe Code

The application runs on Laravel Octane, which boots the app once and reuses it across requests:

- Use `scoped()` instead of `singleton()` for request-scoped bindings.
- Never inject the container, request, or config repository into a singleton constructor.
- Never append to static properties (they persist across requests).

### Mago Linter

PHP linting uses [Mago](https://github.com/carthage-software/mago) with strict rules:

- Controllers must be `final`
- Prefer arrow functions and static closures
- Identity comparison required (`===` / `!==`)
- `declare(strict_types=1)` enforced
- Shell execution, eval, FFI, and globals are prohibited

## Testing

### Running Tests

```bash
# All tests
php artisan test --compact

# Single test by name
php artisan test --compact --filter=testName

# Unit tests with coverage
composer test:unit

# Static analysis (PHPStan level max)
composer test:types

# Mutation testing (100% MSI required)
composer test:mutation

# BDD acceptance tests (Behat)
composer test:features

# Unit tests + static analysis
composer test
```

### Test Strategy

| Layer | Tool | What It Covers |
|---|---|---|
| Unit | PHPUnit 12 | Individual classes and casts |
| Feature | PHPUnit 12 | HTTP requests, model integration |
| Acceptance | Behat 3 | User-facing behaviour (Gherkin scenarios) |
| Static | PHPStan (level max) | Type safety, Octane compatibility, convention enforcement |
| Mutation | Infection | Kill-all-mutants guarantee (100% MSI) |

Tests run against **in-memory SQLite** (`phpunit.xml.dist`). Behat scenarios bootstrap a full Laravel application with `RefreshDatabase`.

### Coverage Requirements

- **100% code coverage** enforced by `robiningelbrecht/phpunit-coverage-tools` (tests fail on low coverage).
- **100% mutation score** enforced by Infection (all mutants must be killed).

## Code Quality

```bash
# Format code
composer format

# Check formatting (CI-friendly)
composer format:check

# Lint
composer lint

# Lint with autofix
composer lint:fix

# Run everything before finishing work
composer before-stopping
```

`composer before-stopping` runs the full quality pipeline: unit tests, static analysis, feature tests, mutation testing, formatting, and linting.

## Static Analysis

PHPStan runs at **level max** with:

- [Larastan](https://github.com/larastan/larastan) for Laravel-specific analysis
- Octane compatibility checks
- Custom disallowed-namespace rules (enforcing Brick\DateTime and custom ULIDs)
- [PSL PHPStan extension](https://github.com/php-standard-library/phpstan-extension) for PHP Standard Library

## Dependencies

### Runtime

| Package | Purpose |
|---|---|
| `laravel/framework` | Core framework |
| `laravel/octane` | High-performance application server |
| `brick/date-time` | Immutable date/time library |
| `gosuperscript/monads` | Monad utilities |
| `php-standard-library/php-standard-library` | PSL utilities |
| `laravel/tinker` | REPL for debugging |

### Development

| Package | Purpose |
|---|---|
| `phpunit/phpunit` | Unit and feature testing |
| `behat/behat` | BDD acceptance testing |
| `infection/infection` | Mutation testing |
| `phpstan/phpstan` + `larastan/larastan` | Static analysis |
| `carthage-software/mago` | PHP linting and formatting |
| `spatie/laravel-ray` | Debug output to Ray |
| `laravel/pail` | Real-time log tailing |

## Deployment

The application includes a FrankenPHP worker script (`public/frankenphp-worker.php`) and is configured for Laravel Octane, making it ready for high-performance deployment via [Laravel Cloud](https://cloud.laravel.com/) or containerised environments.

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).
