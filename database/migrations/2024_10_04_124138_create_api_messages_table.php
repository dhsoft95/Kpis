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
        Schema::connection($this->connection)->create('api_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_key', 50);
            $table->text('message_text');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('api_messages');
    }
};
