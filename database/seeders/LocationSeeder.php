<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('countries')->insert([
            ['name' => 'Indonesia'],
            ['name' => 'Malaysia'],
            ['name' => 'Singapore'],
            ['name' => 'Thailand'],
            ['name' => 'Vietnam'],
            ['name' => 'Philippines'],
            ['name' => 'Brunei'],
            ['name' => 'Myanmar'],
            ['name' => 'Cambodia'],
            ['name' => 'Laos'],
        ]);

        DB::table('provinces')->insert([
            ['id' => 1, 'name' => 'Jawa Barat', 'country_id' => 1],
            ['id' => 2, 'name' => 'Jawa Tengah', 'country_id' => 1],
            ['id' => 3, 'name' => 'Jawa Timur', 'country_id' => 1],
            ['id' => 4, 'name' => 'DKI Jakarta', 'country_id' => 1],
            ['id' => 5, 'name' => 'Banten', 'country_id' => 1],
            ['id' => 6, 'name' => 'Yogyakarta', 'country_id' => 1],
            ['id' => 7, 'name' => 'Sumatera Utara', 'country_id' => 1],
            ['id' => 8, 'name' => 'Sumatera Selatan', 'country_id' => 1],
            ['id' => 9, 'name' => 'Riau', 'country_id' => 1],
            ['id' => 10, 'name' => 'Bali', 'country_id' => 1],
            ['id' => 11, 'name' => 'Lampung', 'country_id' => 1],
            ['id' => 12, 'name' => 'Sulawesi Selatan', 'country_id' => 1],
            ['id' => 13, 'name' => 'Kalimantan Barat', 'country_id' => 1],
            ['id' => 14, 'name' => 'Kalimantan Timur', 'country_id' => 1],
            ['id' => 15, 'name' => 'Papua', 'country_id' => 1],
        ]);

        DB::table('cities')->insert([
            ['name' => 'Bogor', 'province_id' => 1],
            ['name' => 'Cibinong', 'province_id' => 1],
            ['name' => 'Parung', 'province_id' => 1],
            ['name' => 'Ciawi', 'province_id' => 1],
            ['name' => 'Cisarua', 'province_id' => 1],
            ['name' => 'Cileungsi', 'province_id' => 1],
            ['name' => 'Leuwiliang', 'province_id' => 1],
            ['name' => 'Gunung Sindur', 'province_id' => 1],
            ['name' => 'Dramaga', 'province_id' => 1],
            ['name' => 'Megamendung', 'province_id' => 1],
            ['name' => 'Bandung', 'province_id' => 1],
            ['name' => 'Bekasi', 'province_id' => 1],
            ['name' => 'Depok', 'province_id' => 1],
            ['name' => 'Karawang', 'province_id' => 1],
            ['name' => 'Cirebon', 'province_id' => 1],
            ['name' => 'Sukabumi', 'province_id' => 1],
            ['name' => 'Tasikmalaya', 'province_id' => 1],
            ['name' => 'Garut', 'province_id' => 1],
            ['name' => 'Sumedang', 'province_id' => 1],
            ['name' => 'Purwakarta', 'province_id' => 1],
            ['name' => 'Indramayu', 'province_id' => 1],
            ['name' => 'Majalengka', 'province_id' => 1],
            ['name' => 'Kuningan', 'province_id' => 1],
            ['name' => 'Subang', 'province_id' => 1],
            ['name' => 'Bandung Barat', 'province_id' => 1],
            ['name' => 'Ciamis', 'province_id' => 1],
            ['name' => 'Pangandaran', 'province_id' => 1],
        ]);

        DB::table('cities')->insert([
            ['name' => 'Semarang', 'province_id' => 2],
            ['name' => 'Surakarta (Solo)', 'province_id' => 2],
            ['name' => 'Magelang', 'province_id' => 2],
            ['name' => 'Kudus', 'province_id' => 2],
            ['name' => 'Pekalongan', 'province_id' => 2],
            ['name' => 'Tegal', 'province_id' => 2],
            ['name' => 'Cilacap', 'province_id' => 2],
            ['name' => 'Purwokerto', 'province_id' => 2],
            ['name' => 'Salatiga', 'province_id' => 2],
            ['name' => 'Rembang', 'province_id' => 2],
            ['name' => 'Blora', 'province_id' => 2],
            ['name' => 'Jepara', 'province_id' => 2],
            ['name' => 'Demak', 'province_id' => 2],
            ['name' => 'Karanganyar', 'province_id' => 2],
            ['name' => 'Sragen', 'province_id' => 2],
            ['name' => 'Wonogiri', 'province_id' => 2],
            ['name' => 'Sukoharjo', 'province_id' => 2],
            ['name' => 'Klaten', 'province_id' => 2],
            ['name' => 'Wonosobo', 'province_id' => 2],
            ['name' => 'Magelang', 'province_id' => 2],
            ['name' => 'Batang', 'province_id' => 2],
            ['name' => 'Kendal', 'province_id' => 2],
            ['name' => 'Temanggung', 'province_id' => 2],
            ['name' => 'Pemalang', 'province_id' => 2],
            ['name' => 'Pati', 'province_id' => 2],
            ['name' => 'Blora', 'province_id' => 2],
            ['name' => 'Grobogan', 'province_id' => 2],
            ['name' => 'Cilacap', 'province_id' => 2],
            ['name' => 'Kebumen', 'province_id' => 2], 
        ]);

        DB::table('cities')->insert([
            ['name' => 'Surabaya', 'province_id' => 3],
            ['name' => 'Malang', 'province_id' => 3],
            ['name' => 'Banyuwangi', 'province_id' => 3],
            ['name' => 'Jember', 'province_id' => 3],
            ['name' => 'Kediri', 'province_id' => 3],
            ['name' => 'Madiun', 'province_id' => 3],
            ['name' => 'Pasuruan', 'province_id' => 3],
            ['name' => 'Probolinggo', 'province_id' => 3],
            ['name' => 'Blitar', 'province_id' => 3],
            ['name' => 'Medan', 'province_id' => 7],
            ['name' => 'Palembang', 'province_id' => 8],
            ['name' => 'Pekanbaru', 'province_id' => 9],
            ['name' => 'Denpasar', 'province_id' => 10],
            ['name' => 'Bandar Lampung', 'province_id' => 11],
            ['name' => 'Makassar', 'province_id' => 12],
            ['name' => 'Pontianak', 'province_id' => 13],
            ['name' => 'Samarinda', 'province_id' => 14],
            ['name' => 'Jayapura', 'province_id' => 15],
        ]);
    }
}
