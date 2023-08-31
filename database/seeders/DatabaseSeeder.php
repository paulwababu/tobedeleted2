<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // counties
        $this->call(CountiesSeeder::class);
        // roles
        $this->call(RolesSeeder::class);
        $this->call(UnitTypesSeeder::class);
        // users, properties, units
        $this->call(UsersSeeder::class);
        // inquiries seeder
        $this->call(InquiriesSeeder::class);
    }
}
