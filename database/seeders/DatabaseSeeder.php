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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Reinhard Herrmann',
            'email' => 'reinhard-ulrich.herrmann@web.de',
            'password' => bcrypt('Heidi$12'),
        ]);

        $this->call([
            BrandSeeder::class,
            StatusSeeder::class,
            ManufacturerSeeder::class,
            ShiftTypeSeeder::class,
            DisruptionTypeSeeder::class,
            StockSeeder::class,
            TripTypeSeeder::class,
            TruckSeeder::class,

        ]);
    }
}
