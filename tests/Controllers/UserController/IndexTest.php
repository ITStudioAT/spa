<?php

use App\Models\User;
use App\Services\AdminNavigationService;
use Laravel\Sanctum\Sanctum;
use Composer\InstalledVersions;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Set config for pagination
    Config::set('spa.pagination', 15);

    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
});

it('can test index: GET /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $users = User::factory()->count(10)->create();
    $users_count = User::count();
    dump("users_count: $users_count");

    $response = $this->getJson('/api/admin/users')
        ->assertOk();

    $response->assertJsonStructure([
        'pagination' => [
            'current_page',
            'last_page',
            'per_page',
            'total',
            'next_page',
            'prev_page',
        ],
        'items' => [
            '*' => ['id', 'first_name', 'last_name', 'email', 'is_2fa', 'is_active', 'is_confirmed', 'confirmed_at', 'is_verified', 'email_verified_at', 'email_2fa', 'email_2fa_verified_at', 'login_at', 'login_ip']
        ],
    ]);

    $this->assertCount($users_count, $response->json('items'));
});


it('cant test index, users role is not admin: GET /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('moderator');
    Sanctum::actingAs($admin);

    $users = User::factory()
        ->count(10)
        ->create();
    $response = $this->getJson('/api/admin/users')
        ->assertStatus(403);
});


it('can test index, where is_active : GET /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $users = User::factory()
        ->count(10)
        ->state(fn() => ['is_active' => Arr::random([0, 1])])
        ->create();
    $users_count = User::count();

    $response = $this->getJson('/api/admin/users?search_model[is_active]=1')

        ->assertOk();
    $users_count = User::where('is_active', 1)->count();
    dump("users_count: $users_count");

    $this->assertCount($users_count, $response->json('items'));
});


it('can test index, where is_2fa : GET /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $users = User::factory()
        ->count(10)
        ->state(fn() => ['is_2fa' => Arr::random([0, 1])])
        ->create();
    $users_count = User::count();


    $response = $this->getJson('/api/admin/users?search_model[is_active]=1')

        ->assertOk();
    $users_count = User::where('is_active', 1)->count();
    dump("users_count: $users_count");

    $this->assertCount($users_count, $response->json('items'));
});

it('can test index, where search_string : GET /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $users = User::factory()
        ->count(10)
        ->create();
    $users_count = User::count();


    $user = User::find(1);
    $search_string = substr($user->last_name, 1, 3);

    $response = $this->getJson('/api/admin/users?search_model[search_string]=' .  $search_string)
        ->assertOk();

    $users_count = User::where('first_name', 'like', "%{$search_string}%")
        ->orWhere('last_name', 'like', "%{$search_string}%")
        ->orWhere('email', 'like', "%{$search_string}%")->count();

    dump("users_count: $users_count");

    $this->assertCount($users_count, $response->json('items'));
});
