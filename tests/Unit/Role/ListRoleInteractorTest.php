<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use App\UseCases\Role\ListRoleInteractor;

describe('ListRoleInteractor', function () {
    beforeEach(function () {
        $this->interactor = new ListRoleInteractor();
    });

    it('returns all roles', function () {
        Role::create(['name' => 'Role1', 'guard_name' => 'web']);
        Role::create(['name' => 'Role2', 'guard_name' => 'web']);

        $roles = $this->interactor->execute();

        expect($roles)->toHaveCount(2)
            ->and($roles->pluck('name'))->toContain('Role1', 'Role2');
    });
});
