<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name' => 'Indonesia'],
            ['name' => 'United States'],
            ['name' => 'Canada'],
            ['name' => 'United Kingdom'],
            ['name' => 'Spain'],
            ['name' => 'Indian'],
            ['name' => 'China'],
            ['name' => 'Japan'],
            ['name' => 'Korea'],
            ['name' => 'Germany'],
            ['name' => 'Thailand'],
            ['name' => 'Italy'],
        ];

        DB::table('countries')->insert($countries);
    }
}
