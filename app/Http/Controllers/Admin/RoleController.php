<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Helper to group permissions by functional module.
     */
    private function getGroupedPermissions()
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = $parts[0] ?? 'general';
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $groupedPermissions = $this->getGroupedPermissions();
        $users = \App\Models\User::select('name', 'email', 'role')->get();
        
        // Collect ALL distinct user roles directly from users table
        $userRoles = \App\Models\User::whereNotNull('role')
            ->where('role', '!=', '')
            ->distinct()
            ->pluck('role')
            ->map(fn($r) => trim($r))
            ->unique()
            ->values()
            ->all();

        return view('admin.roles.create', compact('groupedPermissions', 'users', 'userRoles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => trim($request->name),
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'តួនាទី (Role) ត្រូវបានបង្កើតដោយជោគជ័យ!');
    }

    public function edit(Role $role)
    {
        $groupedPermissions = $this->getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Protect Admin role name modification if desired
        if ($role->name !== 'Admin' || $request->name === 'Admin') {
            $role->name = trim($request->name);
        }

        $role->save();

        $role->syncPermissions($request->permissions ?? []);

        // Clear Spatie Permission Cache immediately so changes apply across the app
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'តួនាទី (Role) ត្រូវបានកែប្រែដោយជោគជ័យ!');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'មិនអាចលុបតួនាទី Admin បានឡើយ!');
        }

        // Remove role from all users
        foreach ($role->users as $user) {
            $user->removeRole($role->name);
        }
        
        // Also update role column in users table to default 'staff'
        \App\Models\User::where('role', $role->name)
            ->orWhere('role', strtolower($role->name))
            ->update(['role' => 'staff']);

        $role->delete();

        // Clear Spatie Permission Cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'តួនាទី (Role) ត្រូវបានលុបដោយជោគជ័យ!');
    }
}
