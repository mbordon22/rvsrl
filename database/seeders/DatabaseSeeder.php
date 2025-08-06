<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);
        // $this->call(RoleSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(StateSeeder::class);
        $this->call(TipoVehiculoSeeder::class);
        $this->call(TipoCombustibleSeeder::class);
        $this->call(VehiculoSeeder::class);
    }
}
