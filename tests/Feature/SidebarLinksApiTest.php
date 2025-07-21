<?php

use App\Models\User;
use App\Models\SidebarLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage sidebar']);
});

it('returns 401 for unauthenticated users', function () {
    $response = $this->getJson('/api/sidebar-links');
    $response->assertStatus(401);
    $response->assertJson(['message' => 'Unauthenticated.']);
});

it('returns sidebar links for authenticated users', function () {
    $user = User::factory()->create();
    // Give the user all sidebar permissions for test
    SidebarLink::all()->each(function ($link) use ($user) {
        if ($link->url !== '#') {
            $user->givePermissionTo($link->permission_name);
        }
    });
    $this->actingAs($user);
    $response = $this->getJson('/api/sidebar-links');
    $response->assertStatus(200);
    $response->assertJsonStructure([
        '*' => [
            'id',
            'display_name',
            'url',
            'icon_path',
            'children',
        ],
    ]);
});

it('can store a new sidebar link', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('manage sidebar');
    $this->actingAs($user);

    $payload = [
        'display_name' => 'Test Link',
        'url' => '/test-link',
        'icon_path' => 'assets/img/menu-icon/test.svg',
        'parent_id' => null,
        'permission_name' => 'test-link',
    ];

    $response = $this->postJson('/api/sidebar-links', $payload);
    $response->assertStatus(201);
    $response->assertJsonFragment([
        'display_name' => 'Test Link',
        'url' => '/test-link',
    ]);
    $this->assertDatabaseHas('sidebar_links', [
        'display_name' => 'Test Link',
        'url' => '/test-link',
    ]);
});

it('validates required fields when storing sidebar link', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('manage sidebar');
    $this->actingAs($user);

    $response = $this->postJson('/api/sidebar-links', []);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['display_name', 'url']);
});

it('can update a sidebar link', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('manage sidebar');
    $this->actingAs($user);

    $link = SidebarLink::factory()->create([
        'display_name' => 'Old Name',
        'url' => '/old-url',
    ]);

    $payload = [
        'display_name' => 'Updated Name',
        'url' => '/updated-url',
    ];

    $response = $this->putJson("/api/sidebar-links/{$link->id}", $payload);
    $response->assertStatus(200);
    $response->assertJsonFragment([
        'display_name' => 'Updated Name',
        'url' => '/updated-url',
    ]);
    $this->assertDatabaseHas('sidebar_links', [
        'id' => $link->id,
        'display_name' => 'Updated Name',
        'url' => '/updated-url',
    ]);
});

it('can delete a sidebar link', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('manage sidebar');
    $this->actingAs($user);

    $link = SidebarLink::factory()->create();

    $response = $this->deleteJson("/api/sidebar-links/{$link->id}");
    $response->assertStatus(204);
    $this->assertDatabaseMissing('sidebar_links', [
        'id' => $link->id,
    ]);
}); 