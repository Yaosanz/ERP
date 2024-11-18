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
        'gender',
        'address',
        'province',
        'city',
        'country',
        'postal_code',
        'position',
        'salary',
        'hire_date',
        'departments_id',
        'divisions_id',
    ];

    public function payments()
    {
        return $this->hasMany(EmployeePayment::class);
    }
    
    public function department()
    {
        return $this->belongsTo(Departement::class, 'departments_id'); 
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'divisions_id'); 
    }


}
