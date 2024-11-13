<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run()
    {
        $cities = [
            ['name' => 'Jakarta', 'country_id' => 1],
            ['name' => 'Surabaya', 'country_id' => 1],
            ['name' => 'Bandung', 'country_id' => 1],
            ['name' => 'Medan', 'country_id' => 1],
            ['name' => 'Bekasi', 'country_id' => 1],
            ['name' => 'Depok', 'country_id' => 1],
            ['name' => 'Tangerang', 'country_id' => 1],
            ['name' => 'Palembang', 'country_id' => 1],
            ['name' => 'South Tangerang', 'country_id' => 1],
            ['name' => 'Makassar', 'country_id' => 1],
            ['name' => 'Batam', 'country_id' => 1],
            ['name' => 'Pekanbaru', 'country_id' => 1],
            ['name' => 'Bogor', 'country_id' => 1],
            ['name' => 'Bandar Lampung', 'country_id' => 1],
            ['name' => 'Padang', 'country_id' => 1],
            ['name' => 'Malang', 'country_id' => 1],
            ['name' => 'Samarinda', 'country_id' => 1],
            ['name' => 'Tasikmalaya', 'country_id' => 1],
            ['name' => 'Pontianak', 'country_id' => 1],
            ['name' => 'Denpasar', 'country_id' => 1],
            ['name' => 'Banjarmasin', 'country_id' => 1],
            ['name' => 'Semarang', 'country_id' => 1],
            ['name' => 'Balikpapan', 'country_id' => 1],
            ['name' => 'Jambi', 'country_id' => 1],
            ['name' => 'Cirebon', 'country_id' => 1],
            ['name' => 'Mataram', 'country_id' => 1],
            ['name' => 'Manado', 'country_id' => 1],
            ['name' => 'Yogyakarta', 'country_id' => 1],
            ['name' => 'Kupang', 'country_id' => 1],
            ['name' => 'Jayapura', 'country_id' => 1],
            ['name' => 'Ambon', 'country_id' => 1],
            ['name' => 'Palu', 'country_id' => 1],
            ['name' => 'Kendari', 'country_id' => 1],
            ['name' => 'Gorontalo', 'country_id' => 1],
            ['name' => 'Bengkulu', 'country_id' => 1],
            ['name' => 'Ternate', 'country_id' => 1],
            ['name' => 'Tarakan', 'country_id' => 1],
            ['name' => 'Pematangsiantar', 'country_id' => 1],
            ['name' => 'Sibolga', 'country_id' => 1],
            ['name' => 'Belawan', 'country_id' => 1],
            ['name' => 'Binjai', 'country_id' => 1],
            ['name' => 'Padang Sidempuan', 'country_id' => 1],
            ['name' => 'Tebing Tinggi', 'country_id' => 1],
            ['name' => 'Lhokseumawe', 'country_id' => 1],
            ['name' => 'Langsa', 'country_id' => 1],
            ['name' => 'Subulussalam', 'country_id' => 1],
            ['name' => 'Banda Aceh', 'country_id' => 1],
            ['name' => 'Lhokseumawe', 'country_id' => 1],
            ['name' => 'Langsa', 'country_id' => 1],
            ['name' => 'Subulussalam', 'country_id' => 1],
            ['name' => 'Banda Aceh', 'country_id' => 1],
            ['name' => 'Sabang', 'country_id' => 1],
            ['name' => 'Meulaboh', 'country_id' => 1],
            ['name' => 'Denpasar', 'country_id' => 1],
            ['name' => 'Singaraja', 'country_id' => 1],
            ['name' => 'Tabanan', 'country_id' => 1],
            ['name' => 'Negara', 'country_id' => 1],
            ['name' => 'Gianyar', 'country_id' => 1],
            ['name' => 'Bangli', 'country_id' => 1],
            ['name' => 'Karangasem', 'country_id' => 1],
            ['name' => 'Klungkung', 'country_id' => 1],
            ['name' => 'Buleleng', 'country_id' => 1],
            ['name' => 'Mataram', 'country_id' => 1],
            ['name' => 'Praya', 'country_id' => 1],
            ['name' => 'Selong', 'country_id' => 1],
            ['name' => 'Sumbawa Besar', 'country_id' => 1],
            ['name' => 'Bima', 'country_id' => 1],
            
        ];

        DB::table('cities')->insert($cities);
    }
}
