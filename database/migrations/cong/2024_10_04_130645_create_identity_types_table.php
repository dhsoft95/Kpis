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
        Schema::connection($this->connection)->create('identity_types', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('type_name', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('identity_types');
    }
};
