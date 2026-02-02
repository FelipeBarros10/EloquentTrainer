<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires login for challenges', function () {
    $this->get('/challenges')->assertRedirect('/login');
});

it('allows login and shows challenges page', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret1234'),
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret1234',
    ])->assertRedirect('/challenges');

    $this->actingAs($user)->get('/challenges')->assertOk();
});

