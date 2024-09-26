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
        Schema::create('feedback_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedback_question_id'); // Foreign key without constraint
            $table->string('sender_phone');
            $table->integer('rating'); // Assuming rating is an integer
            $table->text('answer')->nullable();
            $table->timestamps();

            // Index for performance (not foreign key constraint)
            $table->index('feedback_question_id');

            $table->unique(['feedback_question_id', 'sender_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_answers');
    }
};
