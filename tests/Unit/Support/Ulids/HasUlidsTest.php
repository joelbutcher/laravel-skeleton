<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Ulids;

use App\Support\Ulids\HasUlids;
use App\Support\Ulids\UlidCast;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class HasUlidsTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class extends Model {
            use HasUlids;

            protected $casts = [
                'id' => UlidCast::class,
            ];
        };
    }

    #[Test]
    public function new_unique_id_returns_valid_ulid_string(): void
    {
        $id = $this->model->newUniqueId();

        $this->assertTrue(Ulid::isValid($id));
        $this->assertSame(strtolower($id), $id);
    }

    #[Test]
    public function get_route_key_returns_uppercased_ulid(): void
    {
        $ulid = new Ulid();
        $this->model->id = $ulid;

        $routeKey = $this->model->getRouteKey();

        $this->assertSame(strtoupper((string) $ulid), $routeKey);
    }

    #[Test]
    public function is_valid_unique_id_returns_true_for_valid_ulid(): void
    {
        $reflection = new \ReflectionMethod($this->model, 'isValidUniqueId');

        $this->assertTrue($reflection->invoke($this->model, (string) new Ulid()));
    }

    #[Test]
    public function is_valid_unique_id_returns_false_for_invalid_string(): void
    {
        $reflection = new \ReflectionMethod($this->model, 'isValidUniqueId');

        $this->assertFalse($reflection->invoke($this->model, 'not-a-ulid'));
    }

    #[Test]
    public function get_route_key_throws_when_key_is_not_a_ulid(): void
    {
        $model = new class extends Model {
            use HasUlids;

            protected $guarded = [];

            public function getKey(): string
            {
                return 'not-a-ulid';
            }
        };

        $this->expectException(InvalidArgumentException::class);

        $model->getRouteKey();
    }

    #[Test]
    public function is_valid_unique_id_is_protected(): void
    {
        $reflection = new \ReflectionMethod($this->model, 'isValidUniqueId');

        $this->assertTrue($reflection->isProtected());
    }
}
