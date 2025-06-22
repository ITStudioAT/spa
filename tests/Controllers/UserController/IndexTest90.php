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
    Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);

    

});

it('can test index: /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

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
            '*' => ['id', 'name']
        ],
    ]);

    // Read roles from DB, excluding 'super_admin' (same as your query)
    $dbRoles = Role::whereNotIn('name', ['super_admin'])
        ->orderBy('name')
        ->paginate(config('spa.pagination'));

    // Extract array of roles from DB (only id and name to compare)
    $dbRolesArray = $dbRoles->items();
    $dbRolesArray = array_map(function ($role) {
        return [
            'id' => $role->id,
            'name' => $role->name,
        ];
    }, $dbRolesArray);

    // Extract roles from API response
    $apiRoles = $response->json('items'); // or 'data' if you use Laravel Resource standard

    // Assert the roles returned by API exactly match the DB roles
    expect($apiRoles)->toEqual($dbRolesArray);
});



it('can test index with search_model: /api/admin/roles', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);

    $response = $this->json('GET', '/api/admin/roles', [
        'search_model' => [
            'search_string' => 'derato',
        ]
    ])->assertOk();

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
            '*' => ['id', 'name']
        ],
    ]);

    $items = $response->json('items');
    $names = collect($items)->pluck('name')->all();

    expect($names)->not->toContain('super_admin');
    expect($names)->not->toContain('_admin');
    expect($names)->toContain('moderator');
    expect($names)->not->toContain('editor');
    expect($names)->not->toContain('user');
});

it('cant test index user not super_admin: /api/admin/roles', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/admin/roles')
        ->assertStatus(403)->assertJson([
            'message' => 'Sie haben keine Berechtigung',
        ]);
});
