<?php

declare(strict_types=1);

namespace App\Support\Ulids;

use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use Illuminate\Support\Str;
use Psl\Str as PslStr;
use Symfony\Component\Uid\Ulid;
use Webmozart\Assert\Assert;

trait HasUlids
{
    use HasUniqueStringIds;

    public function newUniqueId(): string
    {
        return PslStr\uppercase((string) Str::ulid());
    }

    public function getRouteKey(): string
    {
        $key = $this->getKey();
        Assert::isInstanceOf($key, Ulid::class);

        return PslStr\uppercase((string) $key);
    }

    /**
     * @param string $value
     */
    protected function isValidUniqueId($value): bool
    {
        return Str::isUlid($value);
    }
}
