<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use App\UseCases\Role\DeleteRoleInteractor;

describe('DeleteRoleInteractor', function () {
    beforeEach(function () {
        $this->interactor = new DeleteRoleInteractor();
    });

    it('deletes the given role', function () {
        $role = Role::create(['name' => 'DeleteMe', 'guard_name' => 'web']);
        $this->interactor->execute($role);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    });
}); 