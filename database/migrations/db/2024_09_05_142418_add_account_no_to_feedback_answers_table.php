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
            $table->string('account_no')->after('feedback_question_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_answers', function (Blueprint $table) {
            $table->dropColumn('account_no');
        });
    }
};
