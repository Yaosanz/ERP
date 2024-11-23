<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'province_id'
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
