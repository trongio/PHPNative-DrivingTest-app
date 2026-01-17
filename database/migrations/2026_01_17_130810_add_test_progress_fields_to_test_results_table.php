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
        Schema::table('test_results', function (Blueprint $table) {
            // Progress tracking fields for pause/resume functionality
            $table->unsignedInteger('current_question_index')->default(0)->after('time_taken_seconds');
            $table->json('answers_given')->nullable()->after('current_question_index');
            $table->json('skipped_question_ids')->nullable()->after('answers_given');
            $table->timestamp('paused_at')->nullable()->after('skipped_question_ids');
            $table->integer('remaining_time_seconds')->nullable()->after('paused_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn([
                'current_question_index',
                'answers_given',
                'skipped_question_ids',
                'paused_at',
                'remaining_time_seconds',
            ]);
        });
    }
};
