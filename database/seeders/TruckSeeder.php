<?php

namespace Database\Seeders;

use App\Models\Truck;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TruckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Truck::create([
            'id' => 1,
            'manufacturer_id' => 1,
            'type' => 'Mercedes Antos',
            'truck_number' => '0098',
            'licence_plate' => 'PM RB 5098',
            'capacity' => 35,
            'weight' => 27000,
            'height' => 365,
            'is_active' => 1,
            'remark' => 'automatisch erstellt',
        ]);
        Truck::create([
            'id' => 1,
            'manufacturer_id' => 1,
            'type' => 'Mercedes Antos',
            'truck_number' => '0098',
            'licence_plate' => 'PM RB 5098',
            'capacity' => 35,
            'weight' => 27000,
            'height' => 365,
            'is_active' => 1,
            'remark' => 'automatisch erstellt',
        ]);

        Truck::create([
            'id' => 1,
            'manufacturer_id' => 2,
            'type' => 'Mercedes Antos',
            'truck_number' => '0095',
            'licence_plate' => 'PM RB 5095',
            'capacity' => 35,
            'weight' => 27000,
            'height' => 365,
            'is_active' => 1,
            'remark' => 'automatisch erstellt',
        ]);

        Truck::create([
            'id' => 1,
            'manufacturer_id' => 3,
            'type' => 'Mercedes Antos',
            'truck_number' => '0096',
            'licence_plate' => 'PM RB 5096',
            'capacity' => 35,
            'weight' => 27000,
            'height' => 365,
            'is_active' => 1,
            'remark' => 'automatisch erstellt',
        ]);

        Truck::create([
            'id' => 1,
            'manufacturer_id' => 4,
            'type' => 'Mercedes Antos',
            'truck_number' => '0097',
            'licence_plate' => 'PM RB 5097',
            'capacity' => 35,
            'weight' => 27000,
            'height' => 365,
            'is_active' => 1,
            'remark' => 'automatisch erstellt',
        ]);
        Truck::create([
            'id' => 1,
            'manufacturer_id' => 5,
            'type' => 'Mercedes Antos',
            'truck_number' => '0099',
            'licence_plate' => 'PM RB 5099',
            'capacity' => 35,
            'weight' => 27000,
            'height' => 365,
            'is_active' => 1,
            'remark' => 'automatisch erstellt',
        ]);

    }
}
