<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $this->seedSampleData();
        $this->seedAdditionalData();
    }

    private function seedSampleData()
    {
        $sampleData = [
            ['SM1725539915', null, 1, '@67910410150000', 0.00, 0.00, 'TZS', 'TZS', 0.00, '@679104101', null, 0.00, 'CASH IN', null, null, 0.00, 0.00, 0.00, 'SIMBA MONEY', 'Balance Reconciliation', 'NO ACCOUNT', null, 'received', '2024-09-05 15:38:39', '2024-09-05 15:38:39'],
            ['PPSM1725539915', null, 1, '@679104101', 300.00, 35000.00, 'TZS', 'TZS', 0.00, '@677090275', 'David Haule', 0.00, 'WALLET TO WALLET', null, null, 0.00, 0.00, 0.00, 'SIMBA MONEY', 'Wallet to Wallet transaction', '7000028577', null, 'sent', '2024-09-05 15:38:46', '2024-09-05 15:38:46'],
            ['CISM1725539915', null, 16, '@679104101', 1000.00, 0.00, 'TZS', 'TZS', 0.00, '@677090275', 'David Haule', 0.00, 'WALLET TO WALLET', null, null, 0.00, 0.00, 0.00, 'SIMBA MONEY', 'Wallet to Wallet transaction', '7000028624', null, 'sent', '2024-09-05 15:38:49', '2024-09-05 15:38:49'],
            ['PPSM1725540181', null, 16, '@677090275', 6205.00, 1000.00, 'TZS', 'TZS', 0.00, '@679104101', 'James Kanga', 0.00, 'WALLET TO WALLET', null, null, 0.00, 0.00, 0.00, 'SIMBA MONEY', 'Wallet to Wallet transaction', 'NO ACCOUNT', null, 'sent', '2024-09-05 15:43:08', '2024-09-05 15:43:08'],
            ['CISM1725540181', null, 1, '@677090275', 1000.00, 0.00, 'TZS', 'TZS', 0.00, '@679104101', 'James Kanga', 0.00, 'WALLET TO WALLET', null, null, 0.00, 0.00, 0.00, 'SIMBA MONEY', 'Wallet to Wallet transaction', '7000028577', null, 'sent', '2024-09-05 15:43:12', '2024-09-05 15:43:12'],
            ['SM1725607310', null, 12, '+255784670202', 1000.00, 0.00, 'TZS', 'TZS', 0.00, '+255784670202', 'Gasper Mrosso', 0.00, 'TOP UP', null, null, 0.00, 0.00, 0.00, 'AIRTEL', 'Transaction id is invalid', '7000028589', 'AIRTEL', 'failed', '2024-09-06 10:21:50', '2024-09-06 10:21:50'],
            ['SM1725607501', null, 12, '+255784670202', 1000.00, 0.00, 'TZS', 'TZS', 0.00, '+255784670202', 'Gasper Mrosso', 0.00, 'TOP UP', null, null, 0.00, 0.00, 0.00, 'AIRTEL', 'Transaction id is invalid', '7000028589', 'AIRTEL', 'failed', '2024-09-06 10:25:01', '2024-09-06 10:25:01'],
            ['SM1725607541', null, 12, '+255784670202', 2000.00, 0.00, 'TZS', 'TZS', 0.00, '+255784670202', 'Gasper Mrosso', 0.00, 'TOP UP', null, null, 0.00, 0.00, 0.00, 'AIRTEL', 'Transaction id is invalid', '7000028589', 'AIRTEL', 'failed', '2024-09-06 10:25:41', '2024-09-06 10:25:41'],
            ['SMB1725876615', null, 1, '+255679104101', 1000.00, 0.00, 'TZS', 'TZS', 0.00, '+255679104101', 'James Kanga', 0.00, 'TOP UP', null, null, 0.00, 0.00, 0.00, 'TIGO', 'Wallet Top Up', '7000028577', 'TIGO', 'pending', '2024-09-09 13:10:15', '2024-09-09 13:10:15'],
            ['SMB1725870343', '1950370046921', 1, '+255679104101', 1000.00, 0.00, 'TZS', 'TZS', 0.00, '+255679104101', 'James Kanga', 0.00, 'TOP UP', null, null, 0.00, 0.00, 0.00, 'TIGO', 'Wallet Top Up', '7000028577', 'TIGO', 'deposited', '2024-09-09 11:25:43', '2024-09-09 11:25:43'],
        ];

        foreach ($sampleData as $data) {

            DB::connection('mysql_second')->table('tbl_simba_transactions')->insert([
                'trx_id' => $data[0],
                'third_party_trx_id' => $data[1],
                'user_id' => $data[2],
                'txn_source' => $data[3],
                'credit_amount' => $data[4],
                'debit_amount' => $data[5],
                'sender_currency' => $data[6],
                'receiver_currency' => $data[7],
                'charges' => $data[8],
                'txn_destination' => $data[9],
                'receiver_fullname' => $data[10],
                'partner_charges' => $data[11],
                'transaction_type' => $data[12],
                'biller_code' => $data[13],
                'biller_ref' => $data[14],
                'tax' => $data[15],
                'exchange_rate' => $data[16],
                'partner_exchange_rate' => $data[17],
                'partner_name' => $data[18],
                'reason' => $data[19],
                'account_no' => $data[20],
                'network_type' => $data[21],
                'status' => $data[22],
                'created_at' => $data[23],
                'updated_at' => $data[24],
            ]);
        }
    }

    private function seedAdditionalData(): void
    {
        $faker = Faker::create();

        // Generate data for the last 3 months
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        $transactionTypes = ['CASH IN', 'WALLET TO WALLET', 'TOP UP'];
        $partners = ['SIMBA MONEY', 'AIRTEL', 'TIGO'];
        $statuses = ['received', 'sent', 'failed', 'pending', 'deposited'];

        while ($startDate <= $endDate) {
            // Generate 5 transactions per day
            for ($i = 0; $i < 5; $i++) {
                $amount = $faker->randomFloat(2, 100, 50000);
                $isCredit = $faker->boolean();
                $transactionType = $faker->randomElement($transactionTypes);
                $partner = $faker->randomElement($partners);

                // Function to generate either a phone number or a tag
                $generateIdentifier = function () use ($faker) {
                    return $faker->boolean(70)
                        ? '+255' . $faker->numerify('#########')
                        : '@' . $faker->numerify('#########');
                };

                DB::connection('mysql_second')->table('tbl_simba_transactions')->insert([
                    'trx_id' => $faker->regexify('[A-Z]{2,4}') . $faker->numerify('#########'),
                    'third_party_trx_id' => $faker->optional()->numerify('##############'),
                    'user_id' => $faker->numberBetween(1, 20),
                    'txn_source' => $generateIdentifier(),
                    'credit_amount' => $isCredit ? $amount : 0,
                    'debit_amount' => $isCredit ? 0 : $amount,
                    'sender_currency' => 'TZS',
                    'receiver_currency' => 'TZS',
                    'charges' => $faker->randomFloat(2, 0, 100),
                    'txn_destination' => $generateIdentifier(),
                    'receiver_fullname' => $faker->name,
                    'partner_charges' => $faker->randomFloat(2, 0, 50),
                    'transaction_type' => $transactionType,
                    'biller_code' => $faker->optional()->numerify('BILL####'),
                    'biller_ref' => $faker->optional()->numerify('REF####'),
                    'tax' => $faker->randomFloat(2, 0, 50),
                    'exchange_rate' => 1, // Assuming same currency
                    'partner_exchange_rate' => 1, // Assuming same currency
                    'partner_name' => $partner,
                    'reason' => $transactionType . ' transaction',
                    'account_no' => $faker->optional()->numerify('##########'),
                    'network_type' => $partner,
                    'status' => $faker->randomElement($statuses),
                    'created_at' => $startDate,
                    'updated_at' => $startDate,
                ]);
            }

            $startDate->addDay();
        }
    }
}
