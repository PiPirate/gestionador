<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ModuleDemoSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecuta semillas personalizadas solo cuando quieras poblar datos demo manualmente.
        // $this->call(ModuleDemoSeeder::class);
    }
}
