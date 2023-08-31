<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\County;
use App\Models\Photo;
use App\Models\Property;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seed company
        $company = Company::updateOrCreate([
            'msisdn' => '254705799644',
        ],
            [
            'name' => 'Rent East Limited',
            'email' => 'aguvasucyril@gmail.com',
            'location' => 'Lower Kabete, Kiambu County, Kenya',
            'status' => 1,
            'wallet_account_no' => 'WL-'.generateNumbers()
        ]);

        // seed admin
        $admin = User::where('msisdn', '254705799644')->first();
        if(!$admin){
            $user = User::create([
                'company_id' => $company->id,
                'name' => 'Cyril Admin',
                'msisdn' => '254705799644',
                'msisdn_verified_at' => now(),
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'location' => 'Lower Kabete, Kiambu County, Kenya',
                'national_id_no' => '37658736',
                'profile_photo_url' => 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
                'gender' => 1,
                'status' => 1,
                'password' => Hash::make('admin@gmail.com'),
                'remember_token' => Str::random(10),
            ]);
            $user->assignRole('admin');
        }

        // seed owner
        $owner = User::where('msisdn', '254705799645')->first();
        if(!$owner){
            $user = User::create([
                'company_id' => $company->id,
                'name' => 'Cyril Owner',
                'msisdn' => '254705799645',
                'msisdn_verified_at' => now(),
                'email' => 'owner@gmail.com',
                'email_verified_at' => now(),
                'location' => 'Lower kabete, Nairobi County, Kenya',
                'national_id_no' => '23658736',
                'profile_photo_url' => 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
                'gender' => 2,
                'status' => 1,
                'password' => Hash::make('owner@gmail.com'),
                'remember_token' => Str::random(10),
            ]);
            $user->assignRole('owner');
        }

        // seed tenant
        $tenant = User::where('msisdn', '254705799646')->first();
        if(!$tenant){
            $user = User::create([
                'company_id' => $company->id,
                'name' => 'Cyril Tenant',
                'msisdn' => '254705799646',
                'msisdn_verified_at' => now(),
                'email' => 'tenant@gmail.com',
                'email_verified_at' => now(),
                'location' => 'Lower Kabete, Nairobi County, Kenya',
                'national_id_no' => '23658732',
                'profile_photo_url' => 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
                'gender' => 2,
                'status' => 1,
                'password' => Hash::make('tenant@gmail.com'),
                'remember_token' => Str::random(10),
            ]);
            $user->assignRole('tenant');
        }

        // seed tenants (10 records)
        $faker = Faker::create();
        for ($i = 0; $i <= 10; $i++){
            $email = $faker->email;
            $msisdn = $faker->e164PhoneNumber();
            $existing_user = User::where('email', $email)->where('msisdn', $msisdn)->first();
            if ($existing_user){
                continue;
            }
            $user = User::create([
                'company_id' => $company->id,
                'name' => $faker->name,
                'msisdn' => $msisdn,
                'msisdn_verified_at' => now(),
                'email' => $email,
                'email_verified_at' => now(),
                'location' => $faker->streetAddress,
                'national_id_no' => $faker->randomNumber(8),
                'profile_photo_url' => $faker->imageUrl,
                'gender' => 2,
                'status' => 1,
                'password' => Hash::make($email),
                'remember_token' => Str::random(10),
            ]);
            $user->assignRole('tenant');
            // seed properties (10 records)
            for ($j = 0; $j <= 10; $j++){
                $property = Property::create([
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'name' => $faker->buildingNumber.' '.$faker->randomElement(['Plaza', 'Apartments']),
                    'no_of_units' => 10,
                    'type' => $faker->randomElement([1, 2]),
                    'description' => $faker->paragraph,
                    'features' => $faker->paragraph,
                    'county_id' => County::inRandomOrder()->pluck('id')->first(),
                    'location' => $faker->streetAddress,
                    'google_map_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.878026987129!2d36.730923499999996!3d-1.2439453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f18f03d400127%3A0xde39c7c970fda98b!2sFireside%20Group%20Ltd%2C%20HQ!5e0!3m2!1sen!2ske!4v1686689734324!5m2!1sen!2ske" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                    'photo_url' => $faker->imageUrl,
                    'youtube_embed_code' => '<iframe width="1170" height="658" src="https://www.youtube.com/embed/ugsz0ld4VGw" title="What Happens When The Planets Align? | Neil deGrasse Tyson Explains…" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>',
                    'status' => 1
                ]);

                for ($k = 0; $k <= $property->no_of_units; $k++){
                    $deposit = $faker->randomElement([15000, 10000, 20000]);
                    $unit = Unit::create([
                        'property_id' => $property->id,
                        'unit_type_id' => UnitType::inRandomOrder()->pluck('id')->first(),
                        'account_no' => generateNumbers(8),
                        'label' => $faker->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G']).$faker->buildingNumber,
                        'deposit' => $deposit,
                        'rent' => $deposit + 5000,
                        'is_water_postpaid' => $faker->randomElement([false, true]),
                        'is_electricity_postpaid' => $faker->randomElement([false, true]),
                        'features' => $faker->paragraph
                    ]);

                    // seed unit photos (5 records)
                    for ($l = 0; $l <= 5; $l++){
                        Photo::create([
                            'unit_id' => $unit->id,
                            'photo_url' => $faker->imageUrl
                        ]);
                    }
                }
            }
        }
    }
}
