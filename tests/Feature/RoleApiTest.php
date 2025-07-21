<?php

use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a role', function () {
    $response = $this->postJson('/api/roles', [
        'name' => 'Test Role',
        'guard_name' => 'web',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('roles', [
        'name' => 'Test Role',
        'guard_name' => 'web',
    ]);
});

it('can list roles', function () {
    Role::create(['name' => 'Role1', 'guard_name' => 'web']);
    Role::create(['name' => 'Role2', 'guard_name' => 'web']);

    $response = $this->getJson('/api/roles');

    $response->assertOk();
    $response->assertJsonFragment(['name' => 'Role1']);
    $response->assertJsonFragment(['name' => 'Role2']);
});

it('can show a role', function () {
    $role = Role::create(['name' => 'ShowRole', 'guard_name' => 'web']);

    $response = $this->getJson("/api/roles/{$role->id}");

    $response->assertOk();
    $response->assertJsonFragment(['name' => 'ShowRole']);
});

it('can update a role', function () {
    $role = Role::create(['name' => 'OldName', 'guard_name' => 'web']);

    $response = $this->putJson("/api/roles/{$role->id}", [
        'name' => 'NewName',
        'guard_name' => 'web',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => 'NewName',
    ]);
});

it('can delete a role', function () {
    $role = Role::create(['name' => 'DeleteMe', 'guard_name' => 'web']);

    $response = $this->deleteJson("/api/roles/{$role->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('roles', [
        'id' => $role->id,
    ]);
});
