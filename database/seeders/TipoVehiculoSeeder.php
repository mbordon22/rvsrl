<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TipoVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use the DB facade to insert data directly into the database
        DB::table('tipos_vehiculos')->insert([
            ['tipo_vehiculo' => 'Sedan'],
            ['tipo_vehiculo' => 'Pickup'],
            ['tipo_vehiculo' => 'Camión'],
            ['tipo_vehiculo' => 'SUV'],
            ['tipo_vehiculo' => 'Furgoneta']
        ]);
    }
}
