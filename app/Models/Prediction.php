<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $fillable = [
        'kategori',
        'pemasukan',
        'pengeluaran',
        'persentase_perubahan',
        'prediksi_keuntungan',
        'rata_rata_historis_kategori',
        'selisih',
        'status',
        'tanggal_prediksi',
    ];

    public $timestamps = false;
}
