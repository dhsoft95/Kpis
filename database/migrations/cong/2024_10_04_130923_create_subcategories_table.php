<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */


    protected $connection = 'mysql_second';
    public function up(): void
    {
        Schema::connection($this->connection)->create('subcategories', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('category_id');
            $table->string('name', 100);
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('subcategories');
    }
};
