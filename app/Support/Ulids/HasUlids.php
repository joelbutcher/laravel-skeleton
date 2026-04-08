<?php

declare(strict_types=1);

namespace App\Support\Ulids;

use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use Illuminate\Support\Str;
use Symfony\Component\Uid\Ulid;
use Webmozart\Assert\Assert;

trait HasUlids
{
    use HasUniqueStringIds;

    public function newUniqueId(): string
    {
        return strtolower((string) Str::ulid());
    }

    public function getRouteKey(): string
    {
        $key = $this->getKey();
        Assert::isInstanceOf($key, Ulid::class);

        /** @infection-ignore-all UnwrapStrToUpper: Symfony Ulid constructor always uppercases; strtoupper is defensive */
        return strtoupper((string) $key);
    }

    /**
     * @param string $value
     */
    protected function isValidUniqueId($value): bool
    {
        return Str::isUlid($value);
    }
}
