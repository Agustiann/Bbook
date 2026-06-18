<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'borrower_id',
        'book_id',
        'user_id',
        'borrowed_at',
        'returned_at',
        'due_date',
        'late_days',
        'total_fine',
        'status',
        'extension_count',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'returned_at' => 'date',
        'due_date' => 'date',
    ];

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

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class)->withTrashed();
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}