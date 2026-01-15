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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_template_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('test_type', ['quick', 'thematic', 'custom', 'template']);
            $table->foreignId('license_type_id')->nullable()->constrained()->nullOnDelete();
            $table->json('configuration'); // stores test settings used
            $table->json('questions_with_answers'); // stores question IDs with user answers
            $table->unsignedInteger('correct_count');
            $table->unsignedInteger('wrong_count');
            $table->unsignedInteger('total_questions');
            $table->decimal('score_percentage', 5, 2);
            $table->enum('status', ['passed', 'failed', 'abandoned']);
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->integer('time_taken_seconds')->nullable(); // can be negative if overtime
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'test_type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
