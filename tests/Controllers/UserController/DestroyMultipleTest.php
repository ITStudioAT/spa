<?php

use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Composer\InstalledVersions;
use Illuminate\Support\Facades\DB;
use App\Services\AdminNavigationService;
use Illuminate\Database\Eloquent\Relations\Relation;

beforeEach(function () {

    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
});

it('can test destroyMultiple: POST /api/admin/users/destroy_multiple', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user_1 =  User::factory()->create();
    $user_2 =  User::factory()->create();
    $user_3 =  User::factory()->create();
    $user_4 =  User::factory()->create();
    $user_5 =  User::factory()->create();


    $data = [$user_1->id, $user_3->id, $user_5->id];

    $response = $this->postJson('/api/admin/users/destroy_multiple', $data)
        ->assertStatus(204);

    $this->assertDatabaseMissing('users', ['id' => $user_1->id]);
    $this->assertDatabaseMissing('users', ['id' => $user_3->id]);
    $this->assertDatabaseMissing('users', ['id' => $user_5->id]);

    $this->assertDatabaseHas('users', ['id' => $user_2->id]);
    $this->assertDatabaseHas('users', ['id' => $user_4->id]);
});

it('cant test destroyMultiple, wrong role: POST /api/admin/users/destroy_multiple', function () {

    $admin = User::factory()->create();
    $admin->assignRole('moderator');
    Sanctum::actingAs($admin);

    $user_1 =  User::factory()->create();
    $user_2 =  User::factory()->create();
    $user_3 =  User::factory()->create();
    $user_4 =  User::factory()->create();
    $user_5 =  User::factory()->create();


    $data = [$user_1->id, $user_3->id, $user_5->id];

    $response = $this->postJson('/api/admin/users/destroy_multiple', $data)
        ->assertForbidden();
});


it('cant test destroyMultiple, cant delete himself: POST /api/admin/users/destroy_multiple', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user_1 =  User::factory()->create();
    $user_2 =  User::factory()->create();
    $user_3 =  User::factory()->create();
    $user_4 =  User::factory()->create();
    $user_5 =  User::factory()->create();


    $data = [$user_1->id, $user_3->id, $user_5->id, $admin->id];

    $response = $this->postJson('/api/admin/users/destroy_multiple', $data)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Man kann sich selbst nicht löschen',
        ]);

    $this->assertDatabaseHas('users', ['id' => $user_1->id]);
    $this->assertDatabaseHas('users', ['id' => $user_3->id]);
    $this->assertDatabaseHas('users', ['id' => $user_5->id]);

    $this->assertDatabaseHas('users', ['id' => $user_2->id]);
    $this->assertDatabaseHas('users', ['id' => $user_4->id]);
});


it('cant test destroyMultiple, cant delete users with roles: POST /api/admin/users/destroy_multiple', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user_1 =  User::factory()->create();
    $user_2 =  User::factory()->create();
    $user_3 =  User::factory()->create();
    $user_4 =  User::factory()->create();
    $user_5 =  User::factory()->create();
    $user_5->assignRole('moderator');


    $data = [$user_1->id, $user_3->id, $user_5->id];

    $response = $this->postJson('/api/admin/users/destroy_multiple', $data)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Benutzer kann nicht gelöscht werden, da er noch Rollen inne hat.',
        ]);

    $this->assertDatabaseMissing('users', ['id' => $user_1->id]);
    $this->assertDatabaseMissing('users', ['id' => $user_3->id]);
    $this->assertDatabaseHas('users', ['id' => $user_5->id]);

    $this->assertDatabaseHas('users', ['id' => $user_2->id]);
    $this->assertDatabaseHas('users', ['id' => $user_4->id]);
});
