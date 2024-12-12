<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionsIncomes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'date_transaction',
        'description',
        'amount',
        'status',
        'image',
    ];
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
