<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\User;
use Brick\DateTime\LocalDateTime;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[Test]
    public function it_can_be_created_via_factory(): void
    {
        $user = User::factory()->create()->fresh();

        $this->assertModelExists($user);
        $this->assertInstanceOf(Ulid::class, $user->id);
        $this->assertInstanceOf(LocalDateTime::class, $user->email_verified_at);
    }

    #[Test]
    public function it_can_be_created_unverified(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertModelExists($user);
        $this->assertNull($user->email_verified_at);
    }
}
