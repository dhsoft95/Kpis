<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_currency_id');
            $table->unsignedBigInteger('target_currency_id');
            $table->decimal('global_rate', 20, 10);
            $table->decimal('sml_rate', 20, 10);
            $table->boolean('is_flat_adjustment')->default(false);
            $table->unsignedBigInteger('sml_adjustment_id')->nullable();
            $table->unsignedBigInteger('government_tax_id')->nullable();
            $table->timestamp('effective_date');
            $table->timestamps();

            // Unique constraint with a custom, shorter name
            $table->unique(
                ['base_currency_id', 'target_currency_id', 'effective_date'],
                'unique_exchange_rate'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
