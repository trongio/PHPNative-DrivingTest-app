<?php

use App\Models\User;

// Note: Settings are accessed via a Sheet component, not dedicated page routes
// The settings button is a Button element with onClick handler

test('dashboard loads for settings access', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/dashboard');

    $page->assertPathIs('/dashboard')
        ->assertNoJavaScriptErrors();
});
