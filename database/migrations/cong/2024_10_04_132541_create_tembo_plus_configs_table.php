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
        Schema::create('tembo_plus_config', function (Blueprint $table) {
            $table->id();
            $table->string('callback_url', 255)->nullable();
            $table->string('forwarding_secret', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('tembo_plus_config');
    }
};
