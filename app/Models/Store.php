<?php

namespace App\Models;

use Database\Factories\StoreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    /** @use HasFactory<StoreFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'place_id',
        'lat',
        'lon',
        'logo_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lon' => 'float',
        ];
    }

    /**
     * Users associated with the store (owners and employees).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Store owners.
     */
    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /**
     * Store employees.
     */
    public function employees(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'employee');
    }

    /**
     * Services offered by the store.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }

    /**
     * Invite codes generated for this store.
     */
    public function inviteCodes(): HasMany
    {
        return $this->hasMany(StoreInviteCode::class);
    }

    /**
     * Get 2-letter initials for store avatar, stripping mock tags and Portuguese stopwords.
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

        return mb_strtoupper($initials ?: mb_substr($this->name ?? 'CB', 0, 2));
    }
}
