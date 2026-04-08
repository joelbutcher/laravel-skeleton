# Testing Best Practices

## Follow the Testing Pyramid (70/20/10)

Maintain a strict testing pyramid: **70% unit tests, 20% integration tests, 10% E2E tests**.

- **Unit tests** (`tests/Unit/`) — Test individual classes and methods in isolation. Mock external dependencies. These are fast and should form the bulk of the suite.
- **Integration tests** (`tests/Feature/`) — Test component boundaries (HTTP, database, queues). Keep focused on integration points, not business logic.
- **E2E tests** (`features/*.feature`) — Behat scenarios for critical user-facing workflows only. Reserve for high-value acceptance criteria.

Do not write an integration or E2E test when a unit test would suffice. Push logic down to testable, isolated units.

## Use `LazilyRefreshDatabase` Over `RefreshDatabase`

`RefreshDatabase` runs all migrations every test run even when the schema hasn't changed. `LazilyRefreshDatabase` only migrates when needed, significantly speeding up large suites.

## Use Model Assertions Over Raw Database Assertions

Incorrect: `$this->assertDatabaseHas('users', ['id' => $user->id]);`

Correct: `$this->assertModelExists($user);`

More expressive, type-safe, and fails with clearer messages.

## Use Factory States and Sequences

Named states make tests self-documenting. Sequences eliminate repetitive setup.

Incorrect: `User::factory()->create(['email_verified_at' => null]);`

Correct: `User::factory()->unverified()->create();`

## Use `Exceptions::fake()` to Assert Exception Reporting

Instead of `withoutExceptionHandling()`, use `Exceptions::fake()` to assert the correct exception was reported while the request completes normally.

## Call `Event::fake()` After Factory Setup

Model factories rely on model events (e.g., `creating` to generate UUIDs). Calling `Event::fake()` before factory calls silences those events, producing broken models.

Incorrect: `Event::fake(); $user = User::factory()->create();`

Correct: `$user = User::factory()->create(); Event::fake();`

## Use `recycle()` to Share Relationship Instances Across Factories

Without `recycle()`, nested factories create separate instances of the same conceptual entity.

```php
Ticket::factory()
    ->recycle(Airline::factory()->create())
    ->create();
```