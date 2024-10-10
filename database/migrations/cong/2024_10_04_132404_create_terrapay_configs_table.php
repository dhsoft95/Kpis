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
        Schema::create('terrapay_config', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled');
            $table->text('allowed_corridors');
            $table->text('allowed_currencies');
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('terrapay_config');
    }
};
