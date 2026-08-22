<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = $parts[0] ?? 'general';
            $groupedPermissions[$module][] = $permission;
        }

        return view('admin.permissions.index', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'module' => 'required|string',
            'action' => 'required|string',
        ]);

        $name = strtolower(trim($request->module)) . '.' . strtolower(trim($request->action));

        if (Permission::where('name', $name)->exists()) {
            return redirect()->route('admin.permissions.index')->with('error', 'សិទ្ធិនេះមានរួចហើយនៅក្នុងប្រព័ន្ធ!');
        }

        Permission::create([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'សិទ្ធិថ្មីត្រូវបានបន្ថែមដោយជោគជ័យ!');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'សិទ្ធិត្រូវបានលុបដោយជោគជ័យ!');
    }
}
