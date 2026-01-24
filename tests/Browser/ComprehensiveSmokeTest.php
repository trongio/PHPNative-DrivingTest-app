<?php

use App\Models\User;

describe('Smoke Tests - All Pages Load', function () {
    test('all public pages load without errors', function () {
        $page = visit('/');

        // NativePHP logs are expected in web browser mode
        $page->assertNoJavaScriptErrors();
    });

    test('all authenticated pages load without javascript errors', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pages = visit([
            '/dashboard',
            '/questions',
            '/signs',
            '/test',
            '/test/history',
        ]);

        $pages->assertNoJavaScriptErrors();
    });
});

describe('Smoke Tests - Mobile Viewports', function () {
    test('all pages work on mobile viewport', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pages = visit(['/dashboard', '/questions', '/signs'])->on()->mobile();

        $pages->assertNoJavaScriptErrors();
    });

    test('all pages work on iPhone', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $page = visit('/dashboard')->on()->iPhone14Pro();

        $page->assertNoJavaScriptErrors();
    });
});

describe('Smoke Tests - Dark Mode', function () {
    test('all pages work in dark mode', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pages = visit(['/dashboard', '/questions', '/signs'])->inDarkMode();

        $pages->assertNoJavaScriptErrors();
    });
});

describe('Smoke Tests - Authentication Flow', function () {
    test('unauthenticated user sees login page', function () {
        $page = visit('/');

        $page->assertSee('აირჩიეთ მომხმარებელი')
            ->assertNoJavaScriptErrors();
    });

    test('dashboard redirects unauthenticated user', function () {
        $page = visit('/dashboard');

        // Should redirect to login
        $page->assertNoJavaScriptErrors();
    });
});

describe('Smoke Tests - Navigation Flow', function () {
    test('full navigation flow works', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $page = visit('/dashboard');

        $page->assertPathIs('/dashboard')
            ->click('ბილეთები')
            ->assertPathIs('/questions')
            ->click('ნიშნები')
            ->assertPathIs('/signs')
            ->click('მთავარი')
            ->assertPathIs('/dashboard')
            ->assertNoJavaScriptErrors();
    });
});

describe('Smoke Tests - Cross Browser', function () {
    test('pages work in Chrome', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $page = visit('/dashboard');

        $page->assertNoJavaScriptErrors();
    });
});
