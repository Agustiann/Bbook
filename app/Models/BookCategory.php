<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class BookCategory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_name',
        'fine_amount',
        'max_borrow_days',
        'min_stock',
        'max_stock',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public static function getCategoryByStock(int $stock): ?self
    {
        return self::query()
            ->where('min_stock', '<=', $stock)
            ->where(function ($query) use ($stock) {
                $query->where('max_stock', '>=', $stock)
                    ->orWhereNull('max_stock');
            })
            ->first();
    }
}
