<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stock::create([
            'stock_number' => '96020269',
            'name' => 'Lager Mariendorf',
            'is_active' => true,
            'remark' => 'automatisch generiert',
        ]);
        Stock::create([
            'stock_number' => '96020259',
            'name' => 'Lager Oranienburg',
            'is_active' => true,
            'remark' => 'automatisch generiert',
        ]);
        Stock::create([
            'stock_number' => '99999999',
            'name' => 'anderes Lager',
            'is_active' => true,
            'remark' => 'automatisch generiert',
        ]);
    }
}
