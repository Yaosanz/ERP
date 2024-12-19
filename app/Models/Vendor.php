<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    // protected $table = 'vendors';
    protected $fillable = [
        'name',
        'address',
        'number_phone',
        'email',
        'province_id',
        'city_id',
        'country_id',
        'status',
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'province_id');
    }


    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }
}
