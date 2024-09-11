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
        'date',
        'product_name',
        'amount',
        'description',
        'image',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
}
