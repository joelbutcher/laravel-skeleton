<?php

declare(strict_types=1);

namespace App\Support\Ulids;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Eloquent\Model;
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

        /** @infection-ignore-all MethodCallRemoval: Ulid::fromString('') throws the same InvalidArgumentException */
        Assert::stringNotEmpty($value);

        return Ulid::fromString($value);
    }

    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        Assert::isInstanceOf($value, Ulid::class);

        return strtolower((string) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            return strtoupper($value);
        }

        Assert::isInstanceOf($value, Ulid::class);

        /** @infection-ignore-all UnwrapStrToUpper: Symfony Ulid::__toString() already returns uppercase */
        return strtoupper((string) $value);
    }
}
