<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'position',
        'salary',
        'hire_date',
    ];

    public function payments()
    {
        return $this->hasMany(EmployeePayment::class);
    }
}
