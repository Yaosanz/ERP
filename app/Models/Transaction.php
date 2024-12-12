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
        'status',
        'description',
        'image',
    ];

    protected static function booted()
    {
    static::created(function ($transaction) {
        $quantity = $transaction->quantity ?? 0;
        $transaction->updateProductStock(-$quantity);
    });

    static::deleted(function ($transaction) {
        $quantity = $transaction->quantity ?? 0; 
        $transaction->updateProductStock($quantity);
    });

    static::updating(function ($transaction) {
        if ($transaction->isDirty('quantity')) {
            $originalQuantity = $transaction->getOriginal('quantity') ?? 0;
            $quantityChange = $transaction->quantity - $originalQuantity;
            $transaction->updateProductStock(-$quantityChange);
        }
    });
}

    public function updateProductStock(int $quantityChange): void
    {
    if ($this->product) {
        $product = $this->product;

        if ($quantityChange !== 0) {
            $product->stock += $quantityChange;
            $product->save();
        }
    }
}


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    public function scopeExpenses($query)
    {
    return $query->whereHas('category', function ($query) {
        $query->where('is_expense', true);
    })->where('status', 'paid');
    }

    public function scopeIncomes($query)
    {
    return $query->whereHas('category', function ($query) {
        $query->where('is_expense', false);
    })->where('status', 'paid'); 
    }
}