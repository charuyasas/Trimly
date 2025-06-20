<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    /**
     * Assign a role to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRole(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $role = Role::findOrFail($validatedData['role_id']);
        $user->roles()->syncWithoutDetaching($role->id);

        return response()->json(['message' => "Role '{$role->name}' assigned to user '{$user->name}' successfully."]);
    }

    /**
     * Revoke a role from a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeRole(Request $request, User $user, Role $role)
    {
        // Validate if the role_id from URL exists, though route model binding already does this.
        // This is more of a double check or if not using route model binding for the role.
        $request->validate(['role_id' => Rule::exists('roles', 'id')->where(function ($query) use ($role) {
            $query->where('id', $role->id);
        })]);

        if (!$user->roles()->find($role->id)) {
            return response()->json(['message' => "User '{$user->name}' does not have the role '{$role->name}'."], 400);
        }

        $user->roles()->detach($role->id);

        return response()->json(['message' => "Role '{$role->name}' revoked from user '{$user->name}' successfully."]);
    }

    /**
     * Activate a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function activateUser(User $user)
    {
        $user->is_active = true;
        $user->save();
        // Optionally, log this action
        // Log::create([...]);
        return response()->json(['message' => "User '{$user->name}' activated successfully.", 'user' => $user->fresh()]);
    }

    /**
     * Deactivate a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivateUser(User $user)
    {
        // Optional: Prevent deactivating the currently authenticated user, depending on requirements
        // if (Auth::id() === $user->id) {
        //     return response()->json(['message' => 'You cannot deactivate yourself.'], 400);
        // }

        $user->is_active = false;
        $user->save();
        // Optionally, log this action
        // Log::create([...]);
        return response()->json(['message' => "User '{$user->name}' deactivated successfully.", 'user' => $user->fresh()]);
    }

    /**
     * Block a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function blockUser(User $user)
    {
        // Optional: Prevent blocking self or other critical accounts
        // if (Auth::id() === $user->id) {
        //     return response()->json(['message' => 'You cannot block yourself.'], 400);
        // }
        $user->is_blocked = true;
        $user->save();
        // Optionally, log this action
        // Log::create([...]);
        return response()->json(['message' => "User '{$user->name}' blocked successfully.", 'user' => $user->fresh()]);
    }

    /**
     * Unblock a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function unblockUser(User $user)
    {
        $user->is_blocked = false;
        $user->save();
        // Optionally, log this action
        // Log::create([...]);
        return response()->json(['message' => "User '{$user->name}' unblocked successfully.", 'user' => $user->fresh()]);
    }
}
