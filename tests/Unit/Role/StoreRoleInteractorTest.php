<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use App\UseCases\Role\StoreRoleInteractor;

describe('StoreRoleInteractor', function () {
    beforeEach(function () {
        $this->interactor = new StoreRoleInteractor();
    });

    it('creates a new role', function () {
        $data = ['name' => 'TestRole', 'guard_name' => 'web'];
        $role = $this->interactor->execute($data);

        expect($role)->toBeInstanceOf(Role::class);
        $this->assertDatabaseHas('roles', ['name' => 'TestRole', 'guard_name' => 'web']);
    });
}); 