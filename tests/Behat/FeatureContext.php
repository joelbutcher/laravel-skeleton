<?php

declare(strict_types=1);

namespace Tests\Behat;

use App\Models\User;
use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\Uid\Ulid;

final class FeatureContext implements Context
{
    use RefreshDatabase;

    private Application $app;

    private ?TestResponse $response = null;

    private ?User $user = null;

    /** @beforeScenario */
    public function setUp(): void
    {
        $this->app = $this->createApplication();

        $this->refreshDatabase();
    }

    /** @afterScenario */
    public function tearDown(): void
    {
        $this->response = null;
        $this->user = null;
    }

    #[When('I visit the welcome page')]
    public function iVisitTheWelcomePage(): void
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        $request = \Illuminate\Http\Request::create('/', 'GET');

        $response = $kernel->handle($request);

        $this->response = TestResponse::fromBaseResponse($response);
    }

    #[Then('the response status code should be :statusCode')]
    public function theResponseStatusCodeShouldBe(int $statusCode): void
    {
        assert($this->response !== null, 'No response has been received.');

        $this->response->assertStatus($statusCode);
    }

    #[Given('a user exists with name :name and email :email')]
    public function aUserExistsWithNameAndEmail(string $name, string $email): void
    {
        $this->user = User::factory()->create([
            'name' => $name,
            'email' => $email,
        ]);
    }

    #[Given('an unverified user exists with email :email')]
    public function anUnverifiedUserExistsWithEmail(string $email): void
    {
        $this->user = User::factory()
            ->unverified()
            ->create([
                'email' => $email,
            ]);
    }

    #[Then('the user :email should exist in the database')]
    public function theUserShouldExistInTheDatabase(string $email): void
    {
        $user = User::query()->where('email', $email)->first();

        assert($user !== null, "User with email {$email} not found in the database.");
    }

    #[Then('the user should have a valid ULID as their identifier')]
    public function theUserShouldHaveAValidUlidAsTheirIdentifier(): void
    {
        assert($this->user !== null, 'No user has been created.');

        $fresh = $this->user->fresh();

        assert($fresh !== null, 'User could not be refreshed from the database.');
        assert($fresh->id instanceof Ulid, 'User ID is not a valid ULID instance.');
    }

    #[Then('the user :email should not have a verified email')]
    public function theUserShouldNotHaveAVerifiedEmail(string $email): void
    {
        $user = User::query()->where('email', $email)->first();

        assert($user !== null, "User with email {$email} not found in the database.");
        assert($user->email_verified_at === null, 'User email_verified_at should be null.');
    }

    private function createApplication(): Application
    {
        /** @var Application $app */
        $app = require __DIR__ . '/../../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    private function refreshDatabase(): void
    {
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->call('migrate:fresh', ['--force' => true]);
    }
}
