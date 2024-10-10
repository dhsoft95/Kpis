<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigDatabaseSeeder extends Seeder
{
    protected $connection = 'mysql_second';

    public function run()
    {
        $this->seedApiMessages();
        $this->seedCommonPins();
        $this->seedOtpTypes();
        $this->seedTransactionTypes();
        $this->seedIdentityTypes();
        $this->seedCategories();
        $this->seedSubcategories();
        $this->seedUtilityCodes();
        $this->seedTerrapayConfig();
        $this->seedTemboPlusConfig();
        $this->seedSystemDefaults();
    }

    private function seedApiMessages()
    {
        $messages = [
            ['message_key' => 'RETRIEVE_SUCCESS', 'message_text' => 'We successfully retrieved the resource for you!'],
            ['message_key' => 'RETRIEVE_FAILED', 'message_text' => 'Oops! We couldnt fetch the resource. Please try again later.'],
            ['message_key' => 'RETRIEVE_AUTHORIZATION_ERROR', 'message_text' => 'You dont have permission to view this resource.'],
            ['message_key' => 'UPDATE_AUTHORIZATION_ERROR', 'message_text' => 'Youre not authorized to update this resource.'],
            ['message_key' => 'DELETE_AUTHORIZATION_ERROR', 'message_text' => 'Sorry, you don have permission to delete this resource.'],
            // Add more messages here
        ];

        DB::connection($this->connection)->table('api_messages')->insert($messages);
    }

    private function seedCommonPins()
    {
        $pins = [
            ['pin' => '1234'],
            ['pin' => '1111'],
            ['pin' => '0000'],
            ['pin' => '1212'],
            ['pin' => '7777'],
            // Add more common PINs here
        ];

        DB::connection($this->connection)->table('common_pins')->insert($pins);
    }

    private function seedOtpTypes()
    {
        $types = [
            ['type_name' => 'registration'],
            ['type_name' => 'password_reset'],
            ['type_name' => 'transaction'],
            ['type_name' => 'otp'],
        ];

        DB::connection($this->connection)->table('otp_types')->insert($types);
    }

    private function seedTransactionTypes()
    {
        $types = [
            ['type_name' => 'WALLET TO WALLET', 'type_code' => 'wallet_to_wallet'],
            ['type_name' => 'TOP UP', 'type_code' => 'top_up'],
            ['type_name' => 'CASH IN', 'type_code' => 'cash_in'],
            ['type_name' => 'WALLET TO MOBILE', 'type_code' => 'wallet_to_mobile'],
            ['type_name' => 'WALLET TO INTERNATIONAL', 'type_code' => 'wallet_to_international'],
            ['type_name' => 'BILL PAYMENT', 'type_code' => 'bill_payment'],
            ['type_name' => 'WITHDRAWAL', 'type_code' => 'withdrawal'],
            ['type_name' => 'AIRTIME PURCHASE', 'type_code' => 'airtime_purchase'],
        ];

        DB::connection($this->connection)->table('transaction_types')->insert($types);
    }

    private function seedIdentityTypes()
    {
        $types = [
            ['id' => 1, 'type_name' => 'NATIONAL_ID'],
            ['id' => 2, 'type_name' => 'PASSPORT'],
            ['id' => 3, 'type_name' => 'DRIVING_LICENSE'],
            ['id' => 4, 'type_name' => 'VOTER_ID'],
            ['id' => 5, 'type_name' => 'BIRTH_CERTIFICATE'],
            ['id' => 6, 'type_name' => 'RESIDENCE_PERMIT'],
            ['id' => 7, 'type_name' => 'OTHER'],
        ];

        DB::connection($this->connection)->table('identity_types')->insert($types);
    }

    private function seedCategories()
    {
        $categories = [
            ['id' => 1, 'name' => 'Utilities'],
            ['id' => 2, 'name' => 'Government Payment'],
            ['id' => 3, 'name' => 'Entertainment'],
            ['id' => 4, 'name' => 'Health & Insurance'],
            ['id' => 5, 'name' => 'Finance'],
            ['id' => 6, 'name' => 'Travel & Tickets'],
            ['id' => 7, 'name' => 'Merchant Payments'],
            ['id' => 8, 'name' => 'Buy Airtime'],
            ['id' => 9, 'name' => 'Internet'],
        ];

        DB::connection($this->connection)->table('categories')->insert($categories);
    }

    private function seedSubcategories()
    {
        $subcategories = [
            ['id' => 101, 'category_id' => 1, 'name' => 'Water'],
            ['id' => 102, 'category_id' => 1, 'name' => 'Electricity'],
            ['id' => 201, 'category_id' => 2, 'name' => 'Fines'],
            ['id' => 202, 'category_id' => 2, 'name' => 'Parking fees'],
            ['id' => 301, 'category_id' => 3, 'name' => 'Betting'],
            ['id' => 302, 'category_id' => 3, 'name' => 'TV & Subscription'],

        ];

        DB::connection($this->connection)->table('subcategories')->insert($subcategories);
    }

    private function seedUtilityCodes()
    {
        $utilityCodes = [
            ['code' => 'GEPG', 'description' => 'Government Bill Payment'],
            ['code' => 'LUKU', 'description' => 'LUKU'],
            ['code' => 'DSTV', 'description' => 'DSTV Subscription'],
            ['code' => 'DDTVBOX', 'description' => 'DSTV Box Office'],
            ['code' => 'AZAMTV', 'description' => 'AZAM TV Subscription'],
            ['code' => 'STARTIMES', 'description' => 'STAR TIMES'],
            ['code' => 'ZUKU', 'description' => 'ZUKU Subscription'],
            ['code' => 'SMILE', 'description' => 'SMILE 4G Internet'],
            ['code' => 'ZUKUFIBER', 'description' => 'ZUKU Fiber Internet'],
            ['code' => 'TTCL', 'description' => 'TTCL Prepaid and Broadband'],
            ['code' => 'PW', 'description' => 'Precision Air'],
            ['code' => 'COASTAL', 'description' => 'Coastal Aviation'],
            ['code' => 'AURIC', 'description' => 'Auric Air'],
            ['code' => 'UTT', 'description' => 'UTT Amis'],
            ['code' => 'SELCOMPAY', 'description' => 'SelcomPay/Masterpass Merchant Payments'],
        ];

        DB::connection($this->connection)->table('utility_codes')->insert($utilityCodes);

        // Seed the pivot table
        $subcategoryUtilityCodes = [
            ['subcategory_id' => 101, 'utility_code_id' => 1], // Water - GEPG
            ['subcategory_id' => 102, 'utility_code_id' => 2], // Electricity - LUKU
            // Add more relationships here
        ];

        DB::connection($this->connection)->table('subcategory_utility_codes')->insert($subcategoryUtilityCodes);
    }

    private function seedTerrapayConfig()
    {
        DB::connection($this->connection)->table('terrapay_config')->insert([
            'enabled' => false,
            'allowed_corridors' => json_encode(['KE', 'TZ', 'UG', 'RW', 'BI', 'ZW', 'MZ', 'CD', 'ZM', 'MW', 'MG', 'SS']),
            'allowed_currencies' => json_encode(['KES', 'TZS', 'UGX', 'MZN', 'MGA', 'ZMW', 'SSP', 'BIF', 'RWF', 'MWK', 'USD']),
        ]);
    }

    private function seedTemboPlusConfig()
    {
        DB::connection($this->connection)->table('tembo_plus_config')->insert([
            'callback_url' => env('TEMBO_PLUS_CALLBACK_URL', 'https://live.simbamoney.co.tz/public/api/v1/tembo_plus/callback'),
            'forwarding_secret' => env('UAT_FORWARDING_SECRET', 'callback_secret'),
        ]);
    }

    private function seedSystemDefaults(): void
    {
        $defaults = [
            ['key_name' => 'default_first_name', 'value' => 'Simba'],
            ['key_name' => 'default_last_name', 'value' => 'User'],
            ['key_name' => 'default_password', 'value' => 'Simba@2024'],
        ];

        DB::connection($this->connection)->table('system_defaults')->insert($defaults);
    }
}
