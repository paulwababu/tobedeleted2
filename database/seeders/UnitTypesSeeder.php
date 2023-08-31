<?php

namespace Database\Seeders;

use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unit_types = ['Bedsitter', 'One Bedroom', 'Two Bedroom', 'Three Bedroom', 'Office', 'Studio'];
        foreach ($unit_types as $type) {
            UnitType::firstOrCreate([
                'type' => $type
            ]);
        }
    }
}
