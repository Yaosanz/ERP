<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'product_id',
        'date_transaction',
        'product_name',
        'quantity',
        'amount',
        'description',
        'image',
    ];

    protected static function booted()
    {
        static::created(function ($transaction) {
            $transaction->updateProductStock(-$transaction->quantity);
        });

        static::deleted(function ($transaction) {
            $transaction->updateProductStock($transaction->quantity);
        });

        static::updating(function ($transaction) {
            if ($transaction->isDirty('quantity')) {
                $originalQuantity = $transaction->getOriginal('quantity');
                $quantityChange = $transaction->quantity - $originalQuantity;

                $transaction->updateProductStock(-$quantityChange);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function updateProductStock(int $quantityChange): void
    {
        $product = $this->product;

        if ($product) {
            $product->stock += $quantityChange;
            $product->save();
        }
    }

    public function scopeExpenses($query)
    {
        return $query->whereHas('category', function ($query) {
            $query->where('is_expense', true);
        });
    }

    public function scopeIncomes($query)
    {
        return $query->whereHas('category', function ($query) {
            $query->where('is_expense', false);
        });
    }
}