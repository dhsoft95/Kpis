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
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->unsignedBigInteger('percentage_adjustment_id')->nullable()->after('is_flat_adjustment');
            $table->foreign('percentage_adjustment_id')->references('id')->on('percentage_adjustments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropForeign(['percentage_adjustment_id']);
            $table->dropColumn('percentage_adjustment_id');
        });
    }
};
