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
        Schema::connection($this->connection)->create('utility_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->string('description', 255);
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('utility_codes');
    }
};
