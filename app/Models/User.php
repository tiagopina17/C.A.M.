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

#[Fillable(['name', 'email', 'password', 'is_admin'])]
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
            'is_admin' => 'boolean',
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

    /**
     * Get 2-letter initials for user avatar, stripping stopwords.
     */
    public function getInitialsAttribute(): string
    {
        $cleanName = trim(preg_replace('/^\[.*?\]\s*/', '', $this->name ?? ''));
        $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $cleanName);
        $stopwords = ['de', 'da', 'do', 'dos', 'das', 'e', 'a', 'o', '&'];
        $words = array_values(array_filter(
            preg_split('/\s+/', trim($cleanName)),
            fn ($w) => !in_array(mb_strtolower($w), $stopwords) && !empty($w)
        ));

        $initials = '';
        if (!empty($words)) {
            $initials .= mb_substr($words[0], 0, 1);
            if (isset($words[1])) {
                $initials .= mb_substr($words[1], 0, 1);
            }
        }

        return mb_strtoupper($initials ?: mb_substr($this->name ?? 'U', 0, 2));
    }

    /**
     * Determine if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Get user type / badge label in Portuguese.
     */
    public function getRoleLabelAttribute(): string
    {
        if ($this->isAdmin()) {
            return 'Administrador';
        }

        if ($this->ownedStores()->exists() || $this->employedStores()->exists()) {
            return 'Comerciante';
        }

        return 'Cliente';
    }
}
