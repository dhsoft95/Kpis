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
        Schema::create('transaction_charges', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->enum('charge_type', ['fixed', 'percentage', 'both']);
            $table->decimal('fixed_amount', 10, 4)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->unsignedBigInteger('currency_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_charges');
    }
};
