<?php

use App\Enums\TestStatus;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\TestResult;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('Back Button on Dashboard Navigation', function () {
    test('back button from questions returns to dashboard', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->click('ბილეთები')
            ->assertPathIs('/questions');

        $page->script('history.back()');
        $page->wait(1)
            ->assertPathIs('/dashboard')
            ->assertNoJavaScriptErrors();
    });

    test('back button from signs returns to dashboard', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->click('ნიშნები')
            ->assertPathIs('/signs');

        $page->script('history.back()');
        $page->wait(1)
            ->assertPathIs('/dashboard')
            ->assertNoJavaScriptErrors();
    });

    test('back button from test page returns to dashboard', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->navigate('/test')
            ->assertPathIs('/test');

        $page->script('history.back()');
        $page->wait(1)
            ->assertPathIs('/dashboard')
            ->assertNoJavaScriptErrors();
    });
});

describe('Back Button During Active Test', function () {
    beforeEach(function () {
        // Create questions for the test
        $category = QuestionCategory::factory()->create();
        $questions = collect();

        for ($i = 0; $i < 3; $i++) {
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

        $this->activeTest = TestResult::create([
            'user_id' => $this->user->id,
            'test_type' => 'quick',
            'configuration' => ['question_count' => 3, 'time_per_question' => 60, 'failure_threshold' => 10],
            'questions_with_answers' => $questionsWithAnswers,
            'total_questions' => 3,
            'correct_count' => 0,
            'wrong_count' => 0,
            'score_percentage' => 0,
            'status' => TestStatus::InProgress,
            'started_at' => now(),
            'current_question_index' => 0,
            'answers_given' => [],
            'skipped_question_ids' => [],
            'remaining_time_seconds' => 180,
        ]);
    });

    test('active test page loads without errors', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->activeTest->id}");

        $page->assertNoJavaScriptErrors();
    });

    test('active test page handles popstate event', function () {
        $this->actingAs($this->user);

        $page = visit("/test/{$this->activeTest->id}");

        // Simulate back button
        $page->script('history.pushState(null, "", window.location.href)');
        $page->script('history.back()');
        $page->wait(1)
            ->assertNoJavaScriptErrors();
    });
});

describe('Back Button on Mobile', function () {
    test('back button works on mobile viewport', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->on()->mobile();

        $page->click('ბილეთები')
            ->assertPathIs('/questions');

        $page->script('history.back()');
        $page->wait(1)
            ->assertPathIs('/dashboard')
            ->assertNoJavaScriptErrors();
    });

    test('back button works on iPhone', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->on()->iPhone14Pro();

        $page->click('ნიშნები')
            ->assertPathIs('/signs');

        $page->script('history.back()');
        $page->wait(1)
            ->assertPathIs('/dashboard')
            ->assertNoJavaScriptErrors();
    });
});
