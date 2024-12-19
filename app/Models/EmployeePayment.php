<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'payment_date',
        'payment_method',
        'status',
        'image',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function scopeExpenses($query)
    {
        return $query->where('status', 'paid'); 
    }
    public function scopeIncomes($query)
    {
        return $query->where('status', 'received'); 
    }
}
