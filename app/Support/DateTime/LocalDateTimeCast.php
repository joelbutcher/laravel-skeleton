<?php

declare(strict_types=1);

namespace App\Support\DateTime;

use Brick\DateTime\LocalDateTime;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Eloquent\Model;
use Psl\Type;

/** @implements CastsAttributes<LocalDateTime, LocalDateTime> */
final class LocalDateTimeCast implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?LocalDateTime
    {
        return $value ? LocalDateTime::parse(text: Type\string()->assert($value)) : null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value?->toISOString();
    }

    /**
     * @param  LocalDateTime|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value?->toISOString();
    }
}
