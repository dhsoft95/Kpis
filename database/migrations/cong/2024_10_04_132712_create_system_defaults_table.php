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

    public function up()
    {
        Schema::connection($this->connection)->create('system_defaults', function (Blueprint $table) {
            $table->id();
            $table->string('key_name', 50);
            $table->string('value', 255);
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('system_defaults');
    }
};
