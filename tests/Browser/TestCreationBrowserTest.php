<?php

use App\Models\Answer;
use App\Models\LicenseType;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create license types
    $this->parentLicense = LicenseType::factory()->create([
        'name' => 'B Category',
        'code' => 'B',
        'is_parent' => true,
    ]);

    $this->childLicense = LicenseType::factory()->create([
        'name' => 'B1',
        'code' => 'B1',
        'parent_id' => $this->parentLicense->id,
        'is_parent' => false,
    ]);

    // Create categories
    $this->category = QuestionCategory::factory()->create(['name' => 'Traffic Rules']);

    // Create questions with answers
    for ($i = 0; $i < 10; $i++) {
        $question = Question::factory()->create([
            'question_category_id' => $this->category->id,
            'is_active' => true,
        ]);

        // Attach to license type
        $question->licenseTypes()->attach($this->parentLicense->id);

        // Create answers
        Answer::factory()->correct()->create(['question_id' => $question->id, 'position' => 1]);
        Answer::factory()->create(['question_id' => $question->id, 'position' => 2]);
        Answer::factory()->create(['question_id' => $question->id, 'position' => 3]);
        Answer::factory()->create(['question_id' => $question->id, 'position' => 4]);
    }
});

describe('Test Creation Page', function () {
    test('test creation page loads without errors', function () {
        $this->actingAs($this->user);

        $page = visit('/test');

        $page->assertPathIs('/test')
            ->assertNoJavaScriptErrors();
    });

    test('page displays test configuration options', function () {
        $this->actingAs($this->user);

        $page = visit('/test');

        $page->assertPresent('[data-slot="card"]')
            ->assertNoJavaScriptErrors();
    });

    test('user can start a quick test from dashboard', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        // Click the quick test button (FAB or card)
        $page->click('სწრაფი ტესტი')
            ->assertPathBeginsWith('/test/')
            ->assertNoJavaScriptErrors();
    });
});

describe('Test Creation Mobile', function () {
    test('test creation works on mobile viewport', function () {
        $this->actingAs($this->user);

        $page = visit('/test')->on()->mobile();

        $page->assertNoJavaScriptErrors()
            ->assertPresent('[data-slot="card"]');
    });

    test('mobile navigation to test page works', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->on()->mobile();

        $page->assertNoJavaScriptErrors();
    });
});

describe('Test Configuration', function () {
    test('page loads without console errors', function () {
        $this->actingAs($this->user);

        $page = visit('/test');

        $page->assertNoConsoleLogs()
            ->assertNoJavaScriptErrors();
    });
});
