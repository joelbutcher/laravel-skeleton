<?php

declare(strict_types=1);

namespace App\Support\Ulids;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Eloquent\Model;
use Psl\Str;
use Psl\Type;
use Symfony\Component\Uid\Ulid;
use Webmozart\Assert\Assert;

/** @implements CastsAttributes<Ulid, Ulid|string> */
final readonly class UlidCast implements CastsAttributes, SerializesCastableAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Ulid
    {
        if (is_null($value)) {
            return null;
        }

        return Ulid::fromString(Type\string()->assert($value));
    }

    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        Assert::isInstanceOf($value, Ulid::class);

        return Str\lowercase((string) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            return Str\uppercase($value);
        }

        Assert::isInstanceOf($value, Ulid::class);

        return Str\uppercase((string) $value);
    }
}
