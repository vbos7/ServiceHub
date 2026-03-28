<?php

use App\Models\{User};

use function Pest\Laravel\{actingAs, assertDatabaseHas, put};

it('should create a user profile when a user is created', function () {
    $user = User::factory()->create();

    expect($user->userProfile)->not->toBeNull();

    assertDatabaseHas('user_profiles', [
        'user_id' => $user->id,
    ]);
});

it('should be able to update user profile', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->put(route('user-profile.update'), [
            'phone'    => '12999998888',
            'position' => 'Analista de Sistemas',
        ])
        ->assertRedirect();

    assertDatabaseHas('user_profiles', [
        'user_id'  => $user->id,
        'phone'    => '12999998888',
        'position' => 'Analista de Sistemas',
    ]);
});

it('should be able to view user profile', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('user-profile.show'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
            ->component('Profile/Show')
            ->has('userProfile')
        );
});

it('should not allow unauthenticated user to update profile', function () {
    put(route('user-profile.update'), [
        'phone'    => '12999998888',
        'position' => 'Analista',
    ])->assertRedirect(route('login'));
});
