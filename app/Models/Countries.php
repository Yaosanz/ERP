<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function provinces()
    {
        return $this->hasMany(Provinces::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
