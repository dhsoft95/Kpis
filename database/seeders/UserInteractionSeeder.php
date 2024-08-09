<?php

namespace Database\Seeders;

use App\Models\UserInteraction;
use Illuminate\Database\Seeder;
use Faker\Factory;

class UserInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        // Seed 10 random user interactions
        for ($i = 0; $i < 10; $i++) {
            UserInteraction::create([
                'recipient_id' => $faker->numerify('##########'), // Generates a 10-digit number
                'user_message' => $faker->sentence(), // Generates a random sentence
                'bot_response' => $faker->paragraph(), // Generates a random paragraph
            ]);
        }
    }
}
