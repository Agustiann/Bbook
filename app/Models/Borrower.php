<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Borrower extends Authenticatable implements HasAvatar
{
    use SoftDeletes, Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')
            ->withTrashed();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')
            ->withTrashed();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->user?->avatar_url;
    }
    
    public function setAvatarUrlAttribute(?string $value): void
    {
        if (! $this->user) {
            return;
        }

        $this->user->forceFill([
            'avatar_url' => $value,
        ])->save();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->user?->getFilamentAvatarUrl();
    }
}