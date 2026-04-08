<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\DateTime\LocalDateTimeCast;
use App\Support\Ulids\HasUlids;
use App\Support\Ulids\UlidCast;
use Brick\DateTime\LocalDateTime;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Symfony\Component\Uid\Ulid;

/**
 * @property Ulid $id
 * @property string $name
 * @property string $email
 * @property string $remember_token
 * @property LocalDateTime $email_verified_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use HasUlids;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => UlidCast::class,
            'password' => 'hashed',
            'email_verified_at' => LocalDateTimeCast::class,
        ];
    }
}
