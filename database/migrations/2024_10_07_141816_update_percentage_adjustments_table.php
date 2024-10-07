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
        Schema::table('percentage_adjustments', function (Blueprint $table) {
            $table->boolean('is_government_tax')->default(false)->after('percentage');
            $table->string('currency_code', 3)->nullable()->after('is_government_tax');
        });
    }

    public function down()
    {
        Schema::table('percentage_adjustments', function (Blueprint $table) {
            $table->dropColumn('is_government_tax');
            $table->dropColumn('currency_code');
        });
    }
};
