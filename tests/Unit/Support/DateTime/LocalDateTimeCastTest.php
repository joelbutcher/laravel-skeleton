<?php

declare(strict_types=1);

namespace Tests\Unit\Support\DateTime;

use App\Support\DateTime\LocalDateTimeCast;
use Brick\DateTime\LocalDateTime;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LocalDateTimeCastTest extends TestCase
{
    private LocalDateTimeCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new LocalDateTimeCast();
        $this->model = new class extends Model {};
    }

    #[Test]
    public function get_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->get($this->model, 'datetime', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function get_returns_local_date_time_from_string(): void
    {
        $result = $this->cast->get($this->model, 'datetime', '2026-04-07T10:30:00', []);

        $this->assertInstanceOf(LocalDateTime::class, $result);
        $this->assertSame('2026-04-07T10:30', (string) $result);
    }

    #[Test]
    public function set_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->set($this->model, 'datetime', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function set_returns_iso_string(): void
    {
        $dateTime = LocalDateTime::parse('2026-04-07T10:30:00');

        $result = $this->cast->set($this->model, 'datetime', $dateTime, []);

        $this->assertSame('2026-04-07T10:30', $result);
    }

    #[Test]
    public function serialize_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->serialize($this->model, 'datetime', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function serialize_returns_iso_string(): void
    {
        $dateTime = LocalDateTime::parse('2026-04-07T10:30:00');

        $result = $this->cast->serialize($this->model, 'datetime', $dateTime, []);

        $this->assertSame('2026-04-07T10:30', $result);
    }
}
