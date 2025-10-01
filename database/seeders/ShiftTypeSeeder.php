<?php

namespace Database\Seeders;

use App\Models\ShiftType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShiftType::create([
            'id' => 1,
            'code' => 'F3',
            'name' => 'Frühschicht 03:00',
            'start_time' => '03:00',
            'end_time' => '11:33',
            'duration_minutes' => 513,
            'break_minutes' => 45,
            'is_active' => true,
            'remark' => 'Frühdienst 03:00 - 11:33, automatisch generiert',
        ]);
        ShiftType::create([
            'id' => 2,
            'code' => 'F4',
            'name' => 'Frühschicht 04:00',
            'start_time' => '04:00',
            'end_time' => '12:33',
            'duration_minutes' => 513,
            'break_minutes' => 45,
            'is_active' => true,
            'remark' => 'Frühdienst 04:00 - 12:33, automatisch generiert',
        ]);
        ShiftType::create([
            'id' => 3,
            'code' => 'S14',
            'start_time' => '14:00',
            'name' => 'Spätschicht 14:00',
            'end_time' => '21:33',
            'duration_minutes' => 513,
            'break_minutes' => 45,
            'is_active' => true,
            'remark' => 'Spätdienst 14:00 - 21:33, automatisch generiert',
        ]);
        ShiftType::create([
            'id' => 4,
            'code' => 'S15',
            'start_time' => '15:00',
            'name' => 'Spätschicht 15:00',
            'end_time' => '22:33',
            'duration_minutes' => 513,
            'break_minutes' => 45,
            'is_active' => true,
            'remark' => 'Spätdienst 15:00 - 22:33, automatisch generiert',
        ]);
        ShiftType::create([
            'id' => 5,
            'code' => 'T10',
            'name' => 'Tagdienst 10:00',
            'start_time' => '10:00',
            'end_time' => '17:33',
            'duration_minutes' => 513,
            'break_minutes' => 45,
            'is_active' => true,
            'remark' => 'Tagdienst 10:00 - 17:33, automatisch generiert',
        ]);
    }
}
