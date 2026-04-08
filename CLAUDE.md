# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Is

See `README.md` for full domain model, API contract, and integration patterns.

## Common Commands

```bash
# Setup
composer setup                          # Full project setup (install, key, migrate, npm)

# Development
composer run dev                        # Run server, queue, logs, and vite concurrently

# Testing
php artisan test --compact              # Run all tests
php artisan test --compact --filter=testName  # Run a single test
composer test                           # Run unit tests + static analysis
composer test:unit                      # PHPUnit only (with coverage)
composer test:types                     # PHPStan only (level max)
composer test:mutation                  # Infection mutation testing (100% MSI required)
composer test:features                  # Behat feature tests (BDD acceptance tests)

# Code Quality
composer format                         # Mago formatter
composer format:check                   # Mago format check
composer lint                           # Mago linter
composer lint:fix                       # Mago lint + autofix

# Before finishing work
composer before-stopping                # Runs all tests, mutation testing, features, format, and lint
```

## Architecture

### Domain Model

See README.md for full domain model.

### App Structure

- `app/Models/` — Eloquent models
- `app/Support/DateTime/` — `LocalDateCast`, `LocalDateTimeCast` (Brick\DateTime casts)
- `app/Support/Ulids/` — Custom `HasUlids` trait and `UlidCast` (use instead of Laravel's `HasUlids`)
- `routes/web.php`, `routes/console.php` — No API routes file yet; API routes to be added
- `features/` — Behat BDD feature files (Gherkin scenarios)
- `tests/Behat/FeatureContext.php` — Behat step definitions, bootstraps a full Laravel app with `RefreshDatabase`

### Testing Pyramid

Follow a strict testing pyramid — **70% unit, 20% integration, 10% E2E (Behat)**. When adding new functionality:

- **Unit tests first** (`tests/Unit/`) — Test individual classes, methods, and logic in isolation. Mock external dependencies. These should be fast and form the bulk of the test suite.
- **Integration tests second** (`tests/Feature/`) — Test how components work together (HTTP requests, database queries, queue jobs). Use `RefreshDatabase`. Keep these focused on boundaries.
- **E2E tests sparingly** (`features/*.feature`) — Behat scenarios for critical user-facing workflows only. These are slow and brittle; reserve them for high-value acceptance criteria.

Do not write an integration or E2E test when a unit test would suffice. Push logic down to testable units rather than testing everything through HTTP.

### Key Conventions

- **No Carbon/DateTime** — Use `Brick\DateTime\LocalDate` or `Brick\DateTime\LocalDateTime` instead. Enforced by PHPStan disallowed-namespace rules.
- **Custom ULIDs** — Use `App\Support\Ulids\HasUlids` instead of Laravel's `HasUlids`. Enforced by PHPStan.
- **Octane-safe** — App runs on Laravel Octane. Use `scoped()` instead of `singleton()`. Never inject request/container into singleton constructors.
- **Mago linter** — This project uses Mago (`carthage-software/mago`) for PHP linting with strict rules (see `mago.toml`). Controllers must be final, prefer arrow functions, prefer static closures, identity comparison required, strict types enforced.
- **100% mutation coverage** — Infection is configured to require 100% MSI. All mutants must be killed.
- **Coverage enforcement** — PHPUnit uses `robiningelbrecht/phpunit-coverage-tools` to enforce coverage thresholds; tests exit non-zero on low coverage.
- **PHPStan level max** — Includes Larastan, Octane compatibility checks, custom gosuperscript rules, and disallowed-calls enforcement.
- **Tests use in-memory SQLite** — Configured in `phpunit.xml.dist`.
- **Behat BDD tests** — Acceptance-level feature tests written in Gherkin (`features/*.feature`) with step definitions in `tests/Behat/FeatureContext.php`. Behat scenarios bootstrap a full Laravel app and use `RefreshDatabase`.
