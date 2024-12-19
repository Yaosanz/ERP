<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionPayments extends Model
{
    //
    use HasFactory;
    protected $table = 'transactions_payments';
    protected $fillable = [
        'name',
        'category_id',
        'amount',
        'date_transaction',
        'description',
        'status',
        'image',
        'quantity',           
        'vehicle_image',      
        'vehicle_plate',      
        'region',             
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

