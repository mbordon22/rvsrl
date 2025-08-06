<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Or you can create a single record
        Vehiculo::create([
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'ano' => 2020,
            'patente' => 'ABC123',
            'tipo_vehiculo' => 1,
            'tipo_combustible' => 2,
            'estado' => 1,
        ]);
        Vehiculo::create([
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'ano' => 2021,
            'patente' => 'XYZ789',
            'tipo_vehiculo' => 1,
            'tipo_combustible' => 1,
            'estado' => 1,
        ]);
    }
}
