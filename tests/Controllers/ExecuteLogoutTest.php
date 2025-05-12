<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;


it('can logout: /api/admin/execute_logout', function () {

    $user = User::find(1);
    $user->assignRole('admin');


    Sanctum::actingAs($user);
    $response = $this->postJson('/api/admin/execute_logout')
        ->assertOk()
        ->assertJson([
            'message' => 'Logout successful',
        ]);
});


it('cant logout, not logged in: /api/admin/execute_logout', function () {

    $response = $this->postJson('/api/admin/execute_logout')
        ->assertStatus(401);
});
