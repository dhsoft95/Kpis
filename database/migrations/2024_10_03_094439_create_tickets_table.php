<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zendesk_id')->unique();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status');
            $table->string('priority')->nullable();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->timestamp('ticket_created_at')->nullable();
            $table->timestamp('ticket_updated_at')->nullable();
            $table->timestamps();

            $table->index('zendesk_id');
            $table->index('status');
            $table->index('requester_id');
            $table->index('assignee_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
