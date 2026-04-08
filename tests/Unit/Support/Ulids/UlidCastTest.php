<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Ulids;

use App\Support\Ulids\UlidCast;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Psl\Str;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class UlidCastTest extends TestCase
{
    private UlidCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new UlidCast();
        $this->model = new class extends Model {};
    }

    #[Test]
    public function get_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->get($this->model, 'id', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function get_returns_ulid_from_string(): void
    {
        $ulid = new Ulid();

        $result = $this->cast->get($this->model, 'id', (string) $ulid, []);

        $this->assertInstanceOf(Ulid::class, $result);
        $this->assertTrue($ulid->equals($result));
    }

    #[Test]
    public function set_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->set($this->model, 'id', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function set_returns_uppercased_string_when_given_string(): void
    {
        $ulid = new Ulid();

        $result = $this->cast->set($this->model, 'id', Str\lowercase((string) $ulid), []);

        $this->assertSame(Str\uppercase((string) $ulid), $result);
    }

    #[Test]
    public function set_returns_uppercased_string_when_given_ulid(): void
    {
        $ulid = new Ulid();

        $result = $this->cast->set($this->model, 'id', $ulid, []);

        $this->assertSame(Str\uppercase((string) $ulid), $result);
    }

    #[Test]
    public function serialize_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->serialize($this->model, 'id', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function serialize_returns_lowercased_string(): void
    {
        $ulid = new Ulid();

        $result = $this->cast->serialize($this->model, 'id', $ulid, []);

        $this->assertSame(Str\lowercase((string) $ulid), $result);
    }

    #[Test]
    public function serialize_throws_when_value_is_not_a_ulid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cast->serialize($this->model, 'id', 'not-a-ulid', []);
    }

    #[Test]
    public function set_throws_when_value_is_not_a_string_or_ulid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cast->set($this->model, 'id', 12_345, []);
    }
}
