<?php

namespace Database\Seeders;

use App\Models\trans;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            trans::create([
                'sender_phone' => $faker->phoneNumber,
                'receiver_phone' => $faker->phoneNumber,
                'sender_amount' => $faker->randomFloat(2, 1, 1000),
                'receiver_amount' => $faker->randomFloat(2, 1, 1000),
                'status' => rand(1, 3),
                'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);
        }
    }
}
