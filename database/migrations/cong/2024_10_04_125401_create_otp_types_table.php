<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql_second';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('otp_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('otp_types');
    }
};
