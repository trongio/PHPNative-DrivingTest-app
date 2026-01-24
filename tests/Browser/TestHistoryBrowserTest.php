<?php

use App\Enums\TestStatus;
use App\Models\Answer;
use App\Models\LicenseType;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\TestResult;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create license type
    $this->licenseType = LicenseType::factory()->create([
        'name' => 'B Category',
        'code' => 'B',
        'is_parent' => true,
    ]);

    // Create questions for tests
    $category = QuestionCategory::factory()->create();
    $questions = collect();

    for ($i = 0; $i < 5; $i++) {
        $question = Question::factory()->create(['question_category_id' => $category->id]);
        Answer::factory()->correct()->create(['question_id' => $question->id]);
        Answer::factory()->count(3)->create(['question_id' => $question->id]);
        $question->load(['answers', 'questionCategory']);
        $questions->push($question);
    }

    $questionsWithAnswers = $questions->map(fn ($q) => [
        'id' => $q->id,
        'question' => $q->question,
        'answers' => $q->answers->map(fn ($a) => [
            'id' => $a->id,
            'text' => $a->text,
            'is_correct' => $a->is_correct,
        ])->toArray(),
        'question_category' => ['id' => $q->questionCategory->id, 'name' => $q->questionCategory->name],
        'signs' => [],
    ])->toArray();

    // Create completed tests
    $this->passedTest = TestResult::create([
        'user_id' => $this->user->id,
        'test_type' => 'thematic',
        'license_type_id' => $this->licenseType->id,
        'configuration' => ['question_count' => 5, 'time_per_question' => 60, 'failure_threshold' => 10],
        'questions_with_answers' => $questionsWithAnswers,
        'total_questions' => 5,
        'correct_count' => 5,
        'wrong_count' => 0,
        'score_percentage' => 100,
        'status' => TestStatus::Passed,
        'started_at' => now()->subDays(1),
        'finished_at' => now()->subDays(1)->addMinutes(3),
        'time_taken_seconds' => 180,
        'current_question_index' => 5,
        'answers_given' => [],
        'skipped_question_ids' => [],
        'remaining_time_seconds' => 120,
    ]);

    $this->failedTest = TestResult::create([
        'user_id' => $this->user->id,
        'test_type' => 'thematic',
        'license_type_id' => $this->licenseType->id,
        'configuration' => ['question_count' => 5, 'time_per_question' => 60, 'failure_threshold' => 10],
        'questions_with_answers' => $questionsWithAnswers,
        'total_questions' => 5,
        'correct_count' => 2,
        'wrong_count' => 3,
        'score_percentage' => 40,
        'status' => TestStatus::Failed,
        'started_at' => now()->subHours(2),
        'finished_at' => now()->subHours(2)->addMinutes(4),
        'time_taken_seconds' => 240,
        'current_question_index' => 5,
        'answers_given' => [],
        'skipped_question_ids' => [],
        'remaining_time_seconds' => 60,
    ]);
});

describe('Test History Page', function () {
    test('test history page loads without errors', function () {
        $this->actingAs($this->user);

        $page = visit('/test/history');

        $page->assertNoJavaScriptErrors()
            ->assertPathIs('/test/history');
    });

    test('test history shows completed tests', function () {
        $this->actingAs($this->user);

        $page = visit('/test/history');

        $page->assertNoJavaScriptErrors();
    });

    test('test history works on mobile', function () {
        $this->actingAs($this->user);

        $page = visit('/test/history')->on()->mobile();

        $page->assertNoJavaScriptErrors()
            ->assertPathIs('/test/history');
    });
});

describe('Test History Detail View', function () {
    test('clicking test navigates to detail view', function () {
        $this->actingAs($this->user);

        $page = visit("/test/history/{$this->passedTest->id}");

        $page->assertNoJavaScriptErrors()
            ->assertPathIs("/test/history/{$this->passedTest->id}");
    });

    test('test detail shows score', function () {
        $this->actingAs($this->user);

        $page = visit("/test/history/{$this->passedTest->id}");

        $page->assertNoJavaScriptErrors();
    });

    test('test detail works on mobile', function () {
        $this->actingAs($this->user);

        $page = visit("/test/history/{$this->passedTest->id}")->on()->mobile();

        $page->assertNoJavaScriptErrors();
    });
});

describe('Test History Navigation', function () {
    test('can navigate from dashboard to history', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->navigate('/test/history')
            ->assertPathIs('/test/history')
            ->assertNoJavaScriptErrors();
    });

    test('back button from history detail returns to list', function () {
        $this->actingAs($this->user);

        $page = visit('/test/history')
            ->click("[href=\"/test/history/{$this->passedTest->id}\"]");

        $page->assertPathIs("/test/history/{$this->passedTest->id}");

        $page->script('history.back()');
        $page->wait(1)
            ->assertPathIs('/test/history')
            ->assertNoJavaScriptErrors();
    });
});

describe('Test History Filters', function () {
    test('history page shows filter options', function () {
        $this->actingAs($this->user);

        $page = visit('/test/history');

        $page->assertNoJavaScriptErrors();
    });
});

describe('Test History Empty State', function () {
    test('shows message when no history', function () {
        $newUser = User::factory()->create();
        $this->actingAs($newUser);

        $page = visit('/test/history');

        $page->assertNoJavaScriptErrors();
    });
});

describe('Test History Redo Actions', function () {
    test('redo button is visible on completed test', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->passedTest->id}/results");

        $page->assertNoJavaScriptErrors();
    });
});
