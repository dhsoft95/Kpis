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
     Schema::connection('mysql_second')->create('wallet_balances', function (Blueprint $table) {
         $table->id();
         $table->string('partner');
         $table->decimal('balance', 15, 2)->nullable();
         $table->decimal('available_balance', 15, 2)->nullable();
         $table->string('currency')->nullable();
         $table->string('status')->nullable();
         $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_balances');
    }
};
