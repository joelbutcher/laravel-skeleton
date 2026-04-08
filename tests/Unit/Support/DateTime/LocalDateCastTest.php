<?php

declare(strict_types=1);

namespace Tests\Unit\Support\DateTime;

use App\Support\DateTime\LocalDateCast;
use Brick\DateTime\LocalDate;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LocalDateCastTest extends TestCase
{
    private LocalDateCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new LocalDateCast();
        $this->model = new class extends Model {};
    }

    #[Test]
    public function get_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->get($this->model, 'date', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function get_returns_local_date_from_string(): void
    {
        $result = $this->cast->get($this->model, 'date', '2026-04-07', []);

        $this->assertInstanceOf(LocalDate::class, $result);
        $this->assertSame('2026-04-07', (string) $result);
    }

    #[Test]
    public function set_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->set($this->model, 'date', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function set_returns_iso_string(): void
    {
        $date = LocalDate::parse('2026-04-07');

        $result = $this->cast->set($this->model, 'date', $date, []);

        $this->assertSame('2026-04-07', $result);
    }

    #[Test]
    public function serialize_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->serialize($this->model, 'date', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function serialize_returns_iso_string(): void
    {
        $date = LocalDate::parse('2026-04-07');

        $result = $this->cast->serialize($this->model, 'date', $date, []);

        $this->assertSame('2026-04-07', $result);
    }
}
