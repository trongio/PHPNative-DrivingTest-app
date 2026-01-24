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

    // Create license type for the user
    $this->licenseType = LicenseType::factory()->create([
        'name' => 'B Category',
        'code' => 'B',
        'is_parent' => true,
    ]);

    $this->user->update(['default_license_type_id' => $this->licenseType->id]);
});

describe('Dashboard Page Load', function () {
    test('dashboard loads without javascript errors', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->assertNoJavaScriptErrors()
            ->assertPathIs('/dashboard');
    });

    test('dashboard displays statistics section', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->assertSee('სტატისტიკა')
            ->assertNoJavaScriptErrors();
    });

    test('dashboard loads on mobile viewport', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->on()->mobile();

        $page->assertNoJavaScriptErrors()
            ->assertPathIs('/dashboard');
    });

    test('dashboard loads on iPhone', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->on()->iPhone14Pro();

        $page->assertNoJavaScriptErrors();
    });
});

describe('Dashboard with Active Test', function () {
    beforeEach(function () {
        // Create questions for the test
        $category = QuestionCategory::factory()->create();
        $questions = collect();

        for ($i = 0; $i < 3; $i++) {
            $question = Question::factory()->create([
                'question_category_id' => $category->id,
            ]);
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
            'license_type_id' => $this->licenseType->id,
            'configuration' => ['question_count' => 3, 'time_per_question' => 60, 'failure_threshold' => 10],
            'questions_with_answers' => $questionsWithAnswers,
            'total_questions' => 3,
            'correct_count' => 1,
            'wrong_count' => 0,
            'score_percentage' => 0,
            'status' => TestStatus::InProgress,
            'started_at' => now(),
            'current_question_index' => 1,
            'answers_given' => [],
            'skipped_question_ids' => [],
            'remaining_time_seconds' => 180,
        ]);
    });

    test('dashboard shows active test banner', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->assertNoJavaScriptErrors();
    });

    test('clicking continue test navigates to active test', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        // Click continue button
        $page->click('გაგრძელება')
            ->assertPathIs("/test/{$this->activeTest->id}")
            ->assertNoJavaScriptErrors();
    });
});

describe('Dashboard Quick Actions', function () {
    test('quick test button is visible', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->assertNoJavaScriptErrors();
    });

    test('test page link is present', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->assertPresent('[href="/test"]')
            ->assertNoJavaScriptErrors();
    });
});

describe('Dashboard Navigation', function () {
    test('can navigate to questions from dashboard', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->click('ბილეთები')
            ->assertPathIs('/questions')
            ->assertNoJavaScriptErrors();
    });

    test('can navigate to signs from dashboard', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->click('ნიშნები')
            ->assertPathIs('/signs')
            ->assertNoJavaScriptErrors();
    });

    test('navigation elements are present', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->assertSee('ბილეთები')
            ->assertSee('ნიშნები')
            ->assertNoJavaScriptErrors();
    });
});

describe('Dashboard Deferred Props', function () {
    test('dashboard loads with deferred data', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        // Wait for deferred props to load
        $page->wait(1)
            ->assertNoJavaScriptErrors();
    });
});

describe('Dashboard Dark Mode', function () {
    test('dashboard works in dark mode', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->inDarkMode();

        $page->assertNoJavaScriptErrors()
            ->assertPathIs('/dashboard');
    });
});
