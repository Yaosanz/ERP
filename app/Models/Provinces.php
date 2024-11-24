<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinces extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'country_id'
    ];
    public function country()
    {
        return $this->belongsTo(Countries::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function cities()
    {
        return $this->hasMany(Cities::class);
    }
}
