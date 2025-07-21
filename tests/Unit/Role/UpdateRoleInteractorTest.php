<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use App\UseCases\Role\UpdateRoleInteractor;

describe('UpdateRoleInteractor', function () {
    beforeEach(function () {
        $this->interactor = new UpdateRoleInteractor();
    });

    it('updates the given role', function () {
        $role = Role::create(['name' => 'OldName', 'guard_name' => 'web']);
        $data = ['name' => 'NewName', 'guard_name' => 'web'];

        $updated = $this->interactor->execute($role, $data);

        expect($updated->name)->toBe('NewName');
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'NewName']);
    });
});
