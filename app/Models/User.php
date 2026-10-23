<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Stores the user belongs to (as owner or employee).
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Stores owned by this user.
     */
    public function ownedStores(): BelongsToMany
    {
        return $this->stores()->wherePivot('role', 'owner');
    }

    /**
     * Store where the user works as an employee.
     */
    public function employedStores(): BelongsToMany
    {
        return $this->stores()->wherePivot('role', 'employee');
    }
}
