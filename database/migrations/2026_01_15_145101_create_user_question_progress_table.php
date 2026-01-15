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
        Schema::create('user_question_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('times_correct')->default(0);
            $table->unsignedInteger('times_wrong')->default(0);
            $table->boolean('is_bookmarked')->default(false);
            $table->boolean('is_learned')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('last_answered_at')->nullable();
            $table->timestamp('first_answered_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
            $table->index(['user_id', 'is_bookmarked']);
            $table->index(['user_id', 'is_learned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_question_progress');
    }
};
