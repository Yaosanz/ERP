<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $table = 'departments';

   
    public function employees()
    {
        return $this->hasMany(Employee::class, 'departments_id');
    }
    
    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

   
}

