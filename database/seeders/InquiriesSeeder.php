<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\County;
use App\Models\Inquiry;
use App\Models\Photo;
use App\Models\Property;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class InquiriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();
        for ($i = 0; $i < 100; $i++) {
            $random_user = User::whereHas('properties')->inRandomOrder()->first();
            $status = $faker->randomElement([false, true]);
            Inquiry::create([
                'company_id' => $random_user->company_id,
                'property_id' => Property::inRandomOrder()->where('user_id', $random_user->id)->pluck('id')->first(),
                'name' => $faker->name,
                'msisdn' => $faker->phoneNumber,
                'email' => $faker->email,
                'inquiry' => $faker->paragraph,
                'response' => $status ? $faker->paragraph : null,
                'status' => $status,
                'response_date' => $status ? now()->toDateTimeString() : null,
                'response_by' => $status ? $random_user->id : null
            ]);
        }
    }
}
