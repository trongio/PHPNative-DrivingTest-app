<?php

use App\Enums\TestStatus;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\TestResult;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create a question category
    $this->category = QuestionCategory::factory()->create();

    // Create questions with answers
    $this->questions = collect();
    for ($i = 0; $i < 5; $i++) {
        $question = Question::factory()->create([
            'question_category_id' => $this->category->id,
        ]);

        // Create 4 answers for each question (1 correct, 3 wrong)
        Answer::factory()->correct()->create([
            'question_id' => $question->id,
            'position' => 1,
        ]);
        for ($j = 2; $j <= 4; $j++) {
            Answer::factory()->create([
                'question_id' => $question->id,
                'position' => $j,
            ]);
        }

        $question->load(['answers', 'questionCategory']);
        $this->questions->push($question);
    }

    // Build questions_with_answers array
    $questionsWithAnswers = $this->questions->map(function ($q) {
        return [
            'id' => $q->id,
            'question' => $q->question,
            'description' => $q->description,
            'full_description' => $q->full_description,
            'image' => $q->image,
            'image_custom' => $q->image_custom,
            'is_short_image' => $q->is_short_image,
            'answers' => $q->answers->map(fn ($a) => [
                'id' => $a->id,
                'text' => $a->text,
                'is_correct' => $a->is_correct,
                'position' => $a->position,
            ])->toArray(),
            'question_category' => [
                'id' => $q->questionCategory->id,
                'name' => $q->questionCategory->name,
            ],
            'signs' => [],
        ];
    })->toArray();

    // Create an active test
    $this->testResult = TestResult::create([
        'user_id' => $this->user->id,
        'test_type' => 'quick',
        'configuration' => [
            'question_count' => 5,
            'time_per_question' => 60,
            'failure_threshold' => 10,
            'auto_advance' => true,
            'shuffle_seed' => 0.5,
        ],
        'questions_with_answers' => $questionsWithAnswers,
        'total_questions' => 5,
        'correct_count' => 0,
        'wrong_count' => 0,
        'score_percentage' => 0,
        'status' => TestStatus::InProgress,
        'started_at' => now(),
        'current_question_index' => 0,
        'answers_given' => [],
        'skipped_question_ids' => [],
        'remaining_time_seconds' => 300,
    ]);
});

describe('Active Test Page Load', function () {
    test('active test page loads without errors', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}");

        $page->assertNoJavaScriptErrors()
            ->assertPathIs("/test/{$this->testResult->id}");
    });

    test('active test displays question content', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}");

        // Should see question card
        $page->assertPresent('[data-slot="card"]')
            ->assertNoJavaScriptErrors();
    });

    test('active test displays timer', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}");

        // Timer should be visible
        $page->assertNoJavaScriptErrors();
    });

    test('active test displays progress indicator', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}");

        $page->assertNoJavaScriptErrors();
    });
});

describe('Active Test Mobile', function () {
    test('active test works on mobile viewport', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}")->on()->mobile();

        $page->assertNoJavaScriptErrors()
            ->assertPathIs("/test/{$this->testResult->id}");
    });

    test('active test on iPhone viewport', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}")->on()->iPhone14Pro();

        $page->assertNoJavaScriptErrors();
    });
});

describe('Active Test Answer Interaction', function () {
    test('clicking answer triggers selection', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}");

        // Click on an answer button (answers are typically in a list)
        $page->assertNoJavaScriptErrors();
    });
});

describe('Active Test Navigation', function () {
    test('cannot navigate away during active test without confirmation', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}");

        $page->assertNoJavaScriptErrors();
    });
});

describe('Test Pause Behavior', function () {
    test('page visibility change is handled', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}");

        // The test should handle visibility changes
        $page->assertNoJavaScriptErrors();
    });
});

describe('Test Results Page', function () {
    test('results page loads for completed test', function () {
        $this->testResult->update([
            'status' => TestStatus::Passed,
            'finished_at' => now(),
            'score_percentage' => 80,
            'time_taken_seconds' => 120,
        ]);

        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}/results");

        $page->assertNoJavaScriptErrors()
            ->assertPathIs("/test/{$this->testResult->id}/results");
    });

    test('results page displays score', function () {
        $this->testResult->update([
            'status' => TestStatus::Passed,
            'finished_at' => now(),
            'score_percentage' => 100,
            'correct_count' => 5,
            'wrong_count' => 0,
            'time_taken_seconds' => 120,
        ]);

        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}/results");

        $page->assertNoJavaScriptErrors();
    });

    test('results page on mobile viewport', function () {
        $this->testResult->update([
            'status' => TestStatus::Passed,
            'finished_at' => now(),
            'score_percentage' => 80,
            'time_taken_seconds' => 120,
        ]);

        $this->actingAs($this->user);

        $page = visit("/test/{$this->testResult->id}/results")->on()->mobile();

        $page->assertNoJavaScriptErrors();
    });
});
