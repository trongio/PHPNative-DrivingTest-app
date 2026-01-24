<?php

use App\Models\User;

test('logged in user sees dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/dashboard');

    $page->assertSee('სტატისტიკა')
        ->assertNoJavaScriptErrors();
});

test('main pages load without javascript errors', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $pages = visit(['/dashboard', '/questions', '/signs']);

    $pages->assertNoJavaScriptErrors();
});

test('user can navigate to questions page via nav', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/dashboard');

    // The nav item is "ბილეთები" not "კითხვები"
    $page->click('ბილეთები')
        ->assertPathIs('/questions')
        ->assertNoJavaScriptErrors();
});

test('user can navigate to signs page via nav', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/dashboard');

    $page->click('ნიშნები')
        ->assertPathIs('/signs')
        ->assertNoJavaScriptErrors();
});

test('dashboard has navigation elements', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/dashboard');

    // Verify navigation elements are present
    $page->assertSee('ბილეთები')
        ->assertSee('ნიშნები')
        ->assertNoJavaScriptErrors();
});
