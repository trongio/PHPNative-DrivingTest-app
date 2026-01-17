<?php

use App\Models\LicenseType;
use App\Models\QuestionCategory;
use App\Models\TestTemplate;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->licenseType = LicenseType::factory()->create();
    $this->category = QuestionCategory::factory()->create();
});

describe('Template Creation', function () {
    it('can create a template', function () {
        $response = $this->actingAs($this->user)
            ->postJson('/templates', [
                'name' => 'My Test Template',
                'license_type_id' => $this->licenseType->id,
                'question_count' => 30,
                'time_per_question' => 60,
                'failure_threshold' => 10,
                'category_ids' => [$this->category->id],
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'My Test Template',
                'question_count' => 30,
                'time_per_question' => 60,
                'failure_threshold' => 10,
            ]);

        expect(TestTemplate::where('user_id', $this->user->id)->count())->toBe(1);
    });

    it('validates required fields', function () {
        $response = $this->actingAs($this->user)
            ->postJson('/templates', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'question_count', 'time_per_question', 'failure_threshold']);
    });

    it('validates failure_threshold range', function () {
        $response = $this->actingAs($this->user)
            ->postJson('/templates', [
                'name' => 'Test',
                'question_count' => 30,
                'time_per_question' => 60,
                'failure_threshold' => 60, // > 50 should fail
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['failure_threshold']);
    });
});

describe('Template Update', function () {
    it('can update own template', function () {
        $template = TestTemplate::create([
            'user_id' => $this->user->id,
            'name' => 'Old Name',
            'question_count' => 20,
            'time_per_question' => 45,
            'failure_threshold' => 10,
            'category_ids' => [],
            'excluded_question_ids' => [],
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/templates/{$template->id}", [
                'name' => 'New Name',
                'question_count' => 40,
            ]);

        $response->assertSuccessful()
            ->assertJson([
                'name' => 'New Name',
                'question_count' => 40,
            ]);
    });

    it('cannot update other user template', function () {
        $otherUser = User::factory()->create();
        $template = TestTemplate::create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Template',
            'question_count' => 20,
            'time_per_question' => 45,
            'failure_threshold' => 10,
            'category_ids' => [],
            'excluded_question_ids' => [],
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/templates/{$template->id}", [
                'name' => 'Hacked Name',
            ]);

        $response->assertStatus(403);
    });
});

describe('Template Delete', function () {
    it('can delete own template', function () {
        $template = TestTemplate::create([
            'user_id' => $this->user->id,
            'name' => 'To Delete',
            'question_count' => 20,
            'time_per_question' => 45,
            'failure_threshold' => 10,
            'category_ids' => [],
            'excluded_question_ids' => [],
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/templates/{$template->id}");

        $response->assertSuccessful()
            ->assertJson(['success' => true]);

        expect(TestTemplate::find($template->id))->toBeNull();
    });

    it('cannot delete other user template', function () {
        $otherUser = User::factory()->create();
        $template = TestTemplate::create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Template',
            'question_count' => 20,
            'time_per_question' => 45,
            'failure_threshold' => 10,
            'category_ids' => [],
            'excluded_question_ids' => [],
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/templates/{$template->id}");

        $response->assertStatus(403);

        expect(TestTemplate::find($template->id))->not->toBeNull();
    });
});

describe('Template List', function () {
    it('lists only own templates', function () {
        $otherUser = User::factory()->create();

        TestTemplate::create([
            'user_id' => $this->user->id,
            'name' => 'My Template',
            'question_count' => 20,
            'time_per_question' => 45,
            'failure_threshold' => 10,
            'category_ids' => [],
            'excluded_question_ids' => [],
        ]);

        TestTemplate::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Template',
            'question_count' => 20,
            'time_per_question' => 45,
            'failure_threshold' => 10,
            'category_ids' => [],
            'excluded_question_ids' => [],
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/templates');

        $response->assertSuccessful();

        $templates = $response->json();
        expect($templates)->toHaveCount(1);
        expect($templates[0]['name'])->toBe('My Template');
    });
});
