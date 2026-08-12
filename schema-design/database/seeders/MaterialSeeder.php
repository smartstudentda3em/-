<?php

namespace Database\Seeders;

use App\Enums\MaterialUnit;
use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            // name, sku, unit, unit_cost, stock, reorder
            ['A4 White Paper',   'PPR-A4', MaterialUnit::Sheet->value, 0.0500, 50000, 5000],
            ['A3 White Paper',   'PPR-A3', MaterialUnit::Sheet->value, 0.1000, 20000, 2000],
            ['Black Toner',      'TNR-BK', MaterialUnit::Gram->value,  0.8000, 3000,  300],
            ['Color Toner (CMY)', 'TNR-CL', MaterialUnit::Gram->value,  2.5000, 1500,  200],
            ['Spiral Binding',   'BND-SP', MaterialUnit::Piece->value, 1.2000, 800,   100],
            ['Glue Binding',     'BND-GL', MaterialUnit::Piece->value, 0.9000, 600,   80],
            ['Cover Sheet',      'CVR-STD', MaterialUnit::Sheet->value, 0.1500, 10000, 1000],
        ];

        foreach ($materials as [$name, $sku, $unit, $cost, $stock, $reorder]) {
            Material::updateOrCreate(
                ['name' => $name],
                [
                    'sku' => $sku, 'unit' => $unit, 'unit_cost' => $cost,
                    'stock_quantity' => $stock, 'reorder_level' => $reorder, 'is_active' => true,
                ]
            );
        }

        $this->command->info('✔ المواد الخام والمخزون الأولي.');
    }
}
