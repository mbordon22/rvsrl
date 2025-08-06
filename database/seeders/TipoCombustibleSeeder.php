<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TipoCombustibleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use the DB facade to insert data directly into the database
        DB::table('tipos_combustibles')->insert([
            ['tipo_combustible' => 'Nafta'],
            ['tipo_combustible' => 'Diesel'],
            ['tipo_combustible' => 'GNC'],
            ['tipo_combustible' => 'Híbrido'],
            ['tipo_combustible' => 'Eléctrico']
        ]);
    }
}
