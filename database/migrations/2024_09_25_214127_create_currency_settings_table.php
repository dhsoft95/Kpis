<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql_second')->create('currency_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->decimal('value', 10, 4);
            $table->boolean('is_active')->default(true); // Changed status to is_active as boolean
            $table->timestamps();
        });

        // Insert the initial USD determinant
        DB::connection('mysql_second')->table('currency_settings')->insert([
            'key' => 'usd_determinant',
            'value' => 1.4,
            'is_active' => true, // Set the is_active field during insertion
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_second')->dropIfExists('currency_settings');
    }
};
