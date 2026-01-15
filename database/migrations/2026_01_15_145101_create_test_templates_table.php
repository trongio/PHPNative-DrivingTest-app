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
        Schema::create('test_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('license_type_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('question_count')->default(30);
            $table->unsignedInteger('time_per_question')->default(60); // seconds
            $table->unsignedInteger('failure_threshold')->default(10); // percentage
            $table->json('category_ids')->nullable();
            $table->json('excluded_question_ids')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_templates');
    }
};
