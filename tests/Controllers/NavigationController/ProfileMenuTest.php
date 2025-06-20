<?php

use App\Models\User;
use App\Services\AdminNavigationService;
use Laravel\Sanctum\Sanctum;
use Composer\InstalledVersions;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Admin-Rolle anlegen
    Role::firstOrCreate(['name' => 'admin']);
});

it('can test profileMenu: /api/admin/navigation/profile_menu', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $navigationService = new AdminNavigationService();

    $response = $this->getJson('/api/admin/navigation/profile_menu')
        ->assertOk()
        ->assertJson([
            'menu' => $navigationService->profileMenu(),
        ]);
});

it('cant test profileMenu, no user logged in or user no admin: /api/admin/navigation/profile_menu', function () {

    $navigationService = new AdminNavigationService();

    $response = $this->getJson('/api/admin/navigation/profile_menu')
        ->assertStatus(401);

    $admin = User::factory()->create();
    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/admin/navigation/profile_menu')
        ->assertStatus(403);
});
