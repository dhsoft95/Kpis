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
            $table->unsignedBigInteger('user_id')->after('feedback_question_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Remove the account_no column if it exists
            if (Schema::hasColumn('feedback_answers', 'account_no')) {
                $table->dropColumn('account_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('feedback_answers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Add back the account_no column if needed
            $table->string('account_no')->nullable()->after('feedback_question_id');
        });
    }
};
