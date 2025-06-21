<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
// User model is already imported in UserRoleController.php via assignRole/revokeRole type hints

class UserRoleController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Eager load roles to prevent N+1 query problems when accessing user roles.
        $users = User::with('roles')->get();
        return response()->json($users);
    }

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
     * @param  int  $role_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeRole(Request $request, User $user, int $role_id)
    {
        $role = Role::find($role_id);

        if (!$role) {
            return response()->json(['message' => "Role with ID {$role_id} not found."], 404);
        }

        if (!$user->roles()->find($role_id)) {
            return response()->json(['message' => "User '{$user->name}' does not have the role '{$role->name}'."], 400);
        }

        $user->roles()->detach($role_id);

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
