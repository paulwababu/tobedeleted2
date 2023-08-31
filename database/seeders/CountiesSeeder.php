<?php

namespace Database\Seeders;

use App\Models\County;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $counties = [
            "Nairobi",
            "Mombasa",
            "Nakuru",
            "Kisumu",
            "Uasin Gishu",
            "Baringo",
            "Bomet",
            "Bungoma",
            "Busia",
            "Elgeyo Marakwet",
            "Embu",
            "Garissa",
            "Homa Bay",
            "Isiolo",
            "Kajiado",
            "Kakamega",
            "Kericho",
            "Kiambu",
            "Kilifi",
            "Kirinyaga",
            "Kisii",
            "Kitui",
            "Kwale",
            "Laikipia",
            "Lamu",
            "Machakos",
            "Makueni",
            "Mandera",
            "Marsabit",
            "Meru",
            "Migori",
            "Murang'a",
            "Nandi",
            "Narok",
            "Nyamira",
            "Nyandarua",
            "Nyeri",
            "Samburu",
            "Siaya",
            "Taita Taveta",
            "Tana River",
            "Tharaka Nithi",
            "Trans Nzoia",
            "Turkana",
            "Vihiga",
            "Wajir",
            "West Pokot"
        ];

        foreach ($counties as $county){
            County::updateOrCreate([
                'county' => $county,
            ]);
        }
    }
}
