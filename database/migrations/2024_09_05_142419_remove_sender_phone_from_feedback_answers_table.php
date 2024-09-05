<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('feedback_answers', function (Blueprint $table) {
            if (Schema::hasColumn('feedback_answers', 'sender_phone')) {
                $table->dropColumn('sender_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('feedback_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback_answers', 'sender_phone')) {
                $table->string('sender_phone')->after('feedback_question_id')->nullable();
            }
        });
    }
};
