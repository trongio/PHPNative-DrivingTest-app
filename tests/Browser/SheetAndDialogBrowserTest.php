<?php

use App\Models\LicenseType;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create license type
    $this->licenseType = LicenseType::factory()->create([
        'name' => 'B Category',
        'code' => 'B',
        'is_parent' => true,
    ]);

    $this->user->update(['default_license_type_id' => $this->licenseType->id]);
});

describe('Dashboard Layout', function () {
    test('dashboard loads without errors', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard');

        $page->assertSee('სტატისტიკა')
            ->assertNoJavaScriptErrors();
    });

    test('dashboard displays on mobile', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->on()->mobile();

        $page->assertSee('სტატისტიკა')
            ->assertNoJavaScriptErrors();
    });
});

describe('Signs Page', function () {
    test('signs page loads without errors', function () {
        $this->actingAs($this->user);

        $page = visit('/signs');

        $page->assertNoJavaScriptErrors()
            ->assertPathIs('/signs');
    });

    test('signs page works on mobile', function () {
        $this->actingAs($this->user);

        $page = visit('/signs')->on()->mobile();

        $page->assertNoJavaScriptErrors()
            ->assertPathIs('/signs');
    });
});

describe('Filter Controls', function () {
    test('questions page loads filter controls', function () {
        $this->actingAs($this->user);

        $page = visit('/questions');

        $page->assertPathIs('/questions')
            ->assertNoJavaScriptErrors();
    });

    test('questions page works on mobile', function () {
        $this->actingAs($this->user);

        $page = visit('/questions')->on()->mobile();

        $page->assertNoJavaScriptErrors();
    });
});

describe('Mobile Interactions', function () {
    test('dashboard works on iPhone viewport', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->on()->iPhone14Pro();

        $page->assertNoJavaScriptErrors()
            ->assertSee('სტატისტიკა');
    });

    test('dashboard works in dark mode', function () {
        $this->actingAs($this->user);

        $page = visit('/dashboard')->inDarkMode();

        $page->assertNoJavaScriptErrors();
    });
});

describe('Page Load Tests', function () {
    test('main pages load without javascript errors', function () {
        $this->actingAs($this->user);

        $pages = visit(['/dashboard', '/questions', '/signs']);

        $pages->assertNoJavaScriptErrors();
    });
});
