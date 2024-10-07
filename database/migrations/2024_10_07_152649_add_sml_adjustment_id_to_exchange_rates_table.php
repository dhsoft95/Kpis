<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            if (!Schema::hasColumn('exchange_rates', 'sml_adjustment_id')) {
                $table->unsignedBigInteger('sml_adjustment_id')->nullable()->after('is_flat_adjustment');
                $table->foreign('sml_adjustment_id')->references('id')->on('percentage_adjustments')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropForeign(['sml_adjustment_id']);
            $table->dropColumn('sml_adjustment_id');
        });
    }
};
