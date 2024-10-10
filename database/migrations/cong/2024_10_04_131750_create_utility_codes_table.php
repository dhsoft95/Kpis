<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */


    public function up(): void
    {
        Schema::create('utility_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->string('description', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_codes');
    }
};
