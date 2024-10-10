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
        Schema::create('subcategory_utility_codes', function (Blueprint $table) {
            $table->integer('subcategory_id');
            $table->unsignedBigInteger('utility_code_id');
            $table->primary(['subcategory_id', 'utility_code_id']);
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
            $table->foreign('utility_code_id')->references('id')->on('utility_codes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subcategory_utility_codes');
    }
};
