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
        Schema::create('user_statistics', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_tests_taken')->default(0);
            $table->unsignedInteger('total_tests_passed')->default(0);
            $table->unsignedInteger('total_tests_failed')->default(0);
            $table->unsignedInteger('total_questions_answered')->default(0);
            $table->unsignedInteger('total_correct_answers')->default(0);
            $table->decimal('overall_accuracy', 5, 2)->default(0);
            $table->unsignedInteger('current_streak_days')->default(0);
            $table->unsignedInteger('best_streak_days')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->unsignedInteger('total_study_time_seconds')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_statistics');
    }
};
