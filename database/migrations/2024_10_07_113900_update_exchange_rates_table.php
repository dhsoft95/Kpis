<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->boolean('is_flat_adjustment')->default(false)->after('sml_adjustment');
            $table->dropColumn('sml_adjustment_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropColumn('is_flat_adjustment');
            $table->enum('sml_adjustment_type', ['FLAT', 'PERCENTAGE'])->after('sml_adjustment');
        });
    }
}
