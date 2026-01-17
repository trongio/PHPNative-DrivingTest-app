<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * SQLite doesn't support ALTER CONSTRAINT, so we need to recreate the table.
     */
    public function up(): void
    {
        // Disable foreign key checks during table recreation
        DB::statement('PRAGMA foreign_keys = OFF');

        // Create new table with updated CHECK constraints
        DB::statement("
            CREATE TABLE test_results_new (
                id integer primary key autoincrement not null,
                user_id integer not null,
                test_template_id integer,
                test_type varchar check (\"test_type\" in ('quick', 'thematic', 'custom', 'template', 'bookmarked')) not null,
                license_type_id integer,
                configuration text not null,
                questions_with_answers text not null,
                correct_count integer not null,
                wrong_count integer not null,
                total_questions integer not null,
                score_percentage numeric not null,
                status varchar check (\"status\" in ('in_progress', 'paused', 'completed', 'passed', 'failed', 'abandoned')) not null,
                started_at datetime not null,
                finished_at datetime,
                time_taken_seconds integer,
                created_at datetime,
                updated_at datetime,
                current_question_index integer not null default '0',
                answers_given text,
                skipped_question_ids text,
                paused_at datetime,
                remaining_time_seconds integer,
                foreign key(user_id) references users(id) on delete cascade,
                foreign key(test_template_id) references test_templates(id) on delete set null,
                foreign key(license_type_id) references license_types(id) on delete set null
            )
        ");

        // Copy data from old table to new table
        DB::statement('
            INSERT INTO test_results_new
            SELECT * FROM test_results
        ');

        // Drop old table
        DB::statement('DROP TABLE test_results');

        // Rename new table to old name
        DB::statement('ALTER TABLE test_results_new RENAME TO test_results');

        // Recreate indexes
        DB::statement('CREATE INDEX test_results_user_id_created_at_index ON test_results (user_id, created_at)');
        DB::statement('CREATE INDEX test_results_user_id_status_index ON test_results (user_id, status)');
        DB::statement('CREATE INDEX test_results_user_id_test_type_index ON test_results (user_id, test_type)');

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks
        DB::statement('PRAGMA foreign_keys = OFF');

        // Create table with original CHECK constraints
        DB::statement("
            CREATE TABLE test_results_new (
                id integer primary key autoincrement not null,
                user_id integer not null,
                test_template_id integer,
                test_type varchar check (\"test_type\" in ('quick', 'thematic', 'custom', 'template')) not null,
                license_type_id integer,
                configuration text not null,
                questions_with_answers text not null,
                correct_count integer not null,
                wrong_count integer not null,
                total_questions integer not null,
                score_percentage numeric not null,
                status varchar check (\"status\" in ('passed', 'failed', 'abandoned')) not null,
                started_at datetime not null,
                finished_at datetime,
                time_taken_seconds integer,
                created_at datetime,
                updated_at datetime,
                current_question_index integer not null default '0',
                answers_given text,
                skipped_question_ids text,
                paused_at datetime,
                remaining_time_seconds integer,
                foreign key(user_id) references users(id) on delete cascade,
                foreign key(test_template_id) references test_templates(id) on delete set null,
                foreign key(license_type_id) references license_types(id) on delete set null
            )
        ");

        // Copy data (will fail if any rows have new status values)
        DB::statement('
            INSERT INTO test_results_new
            SELECT * FROM test_results
            WHERE status IN (\'passed\', \'failed\', \'abandoned\')
        ');

        // Drop current table
        DB::statement('DROP TABLE test_results');

        // Rename new table
        DB::statement('ALTER TABLE test_results_new RENAME TO test_results');

        // Recreate indexes
        DB::statement('CREATE INDEX test_results_user_id_created_at_index ON test_results (user_id, created_at)');
        DB::statement('CREATE INDEX test_results_user_id_status_index ON test_results (user_id, status)');
        DB::statement('CREATE INDEX test_results_user_id_test_type_index ON test_results (user_id, test_type)');

        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
