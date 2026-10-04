<?php

use App\Models\User;

test('the home page sends guests to the login page', function () {
    $this->get('/')->assertRedirect('/bookmarks');
    $this->get('/bookmarks')->assertRedirect(route('login'));
});

test('the home page sends signed in users to their bookmarks', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertRedirect('/bookmarks');
});
