<?php

namespace Database\Seeders;

use App\Models\feedback_answers;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeedbackAnswersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            feedback_answers::create([
                'feedback_question_id' => rand(1, 9),
                'sender_phone' => $faker->phoneNumber,
                'rating' => rand(1, 10),
                'answer' => $faker->sentence,
                'created_at' => $faker->dateTimeBetween('-2 weeks', 'now'),
            ]);
        }
    }
}
