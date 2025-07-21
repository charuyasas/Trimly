<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use App\UseCases\Role\ShowRoleInteractor;

describe('ShowRoleInteractor', function () {
    beforeEach(function () {
        $this->interactor = new ShowRoleInteractor();
    });

    it('returns the given role', function () {
        $role = Role::create(['name' => 'ShowMe', 'guard_name' => 'web']);

        $result = $this->interactor->execute($role);

        expect($result->id)->toBe($role->id)
            ->and($result->name)->toBe('ShowMe');
    });
});
