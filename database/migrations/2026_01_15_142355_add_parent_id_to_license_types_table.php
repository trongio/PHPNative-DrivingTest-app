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
        Schema::table('license_types', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('name')
                ->constrained('license_types')
                ->onDelete('set null');
            $table->boolean('is_parent')->default(false)->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_types', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_parent']);
        });
    }
};
