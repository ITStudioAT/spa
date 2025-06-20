<?php

use App\Models\User;
use App\Services\AdminNavigationService;
use Laravel\Sanctum\Sanctum;
use Composer\InstalledVersions;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Admin-Rolle anlegen
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'super_admin']);
});

it('can test userMenu: /api/admin/navigation/user_menu', function () {

    $navigationService = new AdminNavigationService();

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    // Checking as admin
    $response = $this->getJson('/api/admin/navigation/user_menu')
        ->assertOk()
        ->assertJson([
            'menu' => $navigationService->userMenu(),
        ]);

    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);

    // Checking as super_admin
    $response = $this->getJson('/api/admin/navigation/user_menu')
        ->assertOk()
        ->assertJson([
            'menu' => $navigationService->userMenu(),
        ]);
});

it('cant test profileMenu, no user logged in or user no admin: /api/admin/navigation/user_menu', function () {

    $navigationService = new AdminNavigationService();

    $response = $this->getJson('/api/admin/navigation/user_menu')
        ->assertStatus(401);

    $admin = User::factory()->create();
    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/admin/navigation/user_menu')
        ->assertStatus(403);
});
