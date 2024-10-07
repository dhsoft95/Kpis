<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TicketsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Generate data for the last 60 days
        $startDate = Carbon::now()->subDays(59)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $priorities = ['low', 'normal', 'urgent'];
        $statuses = ['open', 'closed', 'pending'];

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Generate a random number of tickets for each day (between 50 and 200)
            $ticketCount = $faker->numberBetween(50, 200);

            for ($i = 0; $i < $ticketCount; $i++) {
                $priority = $faker->randomElement($priorities);
                $createdAt = $currentDate->copy()->addMinutes($faker->numberBetween(0, 1439)); // Random time within the day

                DB::table('tickets')->insert([
                    'zendesk_id' => $faker->unique()->numberBetween(1000000, 9999999),
                    'subject' => $faker->sentence(),
                    'description' => $faker->paragraph(),
                    'status' => $faker->randomElement($statuses),
                    'priority' => $priority,
                    'requester_id' => $faker->numberBetween(1000, 9999),
                    'assignee_id' => $faker->optional(0.7)->numberBetween(100, 999), // 30% chance of being unassigned
                    'ticket_created_at' => $createdAt,
                    'ticket_updated_at' => $createdAt->addMinutes($faker->numberBetween(0, 1440)), // Random update time within 24 hours
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            $currentDate->addDay();
        }
    }
}
