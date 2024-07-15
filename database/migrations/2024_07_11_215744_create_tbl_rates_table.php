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
        Schema::create('tbl_rates', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('customer_name');
            $table->decimal('rate', 8, 2); // Adjust precision and scale as needed
            $table->unsignedBigInteger('rate_category_id');
            $table->timestamps();
            // Foreign key constraint
            $table->foreign('rate_category_id')->references('id')->on('tbl_rate_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_rates');
    }
};
