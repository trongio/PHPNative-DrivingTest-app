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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('default_license_type_id')
                ->nullable()
                ->after('question_filter_preferences')
                ->constrained('license_types')
                ->nullOnDelete();
            $table->boolean('test_auto_advance')->default(true)->after('default_license_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['default_license_type_id']);
            $table->dropColumn(['default_license_type_id', 'test_auto_advance']);
        });
    }
};
