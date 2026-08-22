<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $profileImagePath = $request->hasFile('profile_image')
            ? $request->file('profile_image')->store('users', 'public')
            : null;

        $roleName = trim($request->role);
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => strtolower($roleName),
            'profile_image' => $profileImagePath,
        ]);

        $user->syncRoles([$role->name]);

        return redirect()->route('admin.users.index')->with('success', 'អ្នកប្រើប្រាស់ត្រូវបានបង្កើតដោយជោគជ័យ!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = $user->roles->first()?->name ?? $user->role;

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email']);
        $data['role'] = strtolower($request->role);

        // Handle image removal
        if ($request->input('remove_image') == '1') {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $data['profile_image'] = null;
        }
        // Handle new image upload
        elseif ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('users', 'public');
        }

        $roleName = trim($request->role);
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        $data['role'] = strtolower($roleName);

        $user->update($data);
        $user->syncRoles([$role->name]);

        return redirect()->route('admin.users.index')->with('success', 'ព័ត៌មានអ្នកប្រើប្រាស់ត្រូវបានកែប្រែដោយជោគជ័យ!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'អ្នកប្រើប្រាស់ត្រូវបានលុបដោយជោគជ័យ!');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('password')]);
        return redirect()->route('admin.users.index')->with('success', 'ពាក្យសម្ងាត់ត្រូវបានកំណត់ឡើងវិញទៅជា "password"');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('customers.trash', ['tab' => 'users'])->with('success', 'គណនីត្រូវស្តារឡើងវិញដោយជោគជ័យ!');
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }
        $user->forceDelete();
        return redirect()->route('customers.trash', ['tab' => 'users'])->with('success', 'គណនីត្រូវលុបជាស្ថាពរ!');
    }
}
