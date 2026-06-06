<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserApiController extends Controller
{
    // GET /api/users — list all users (admin only)
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = User::select('id', 'name', 'email', 'role', 'profile_image', 'created_at', 'updated_at')
            ->get()
            ->map(fn($u) => $this->format($u));

        return response()->json(['data' => $users]);
    }

    // GET /api/users/{id} — get a specific user (admin or self)
    public function show(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin' && $request->user()->id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['data' => $this->format($user)]);
    }

    // GET /api/user — get authenticated user's own profile
    public function me(Request $request)
    {
        return response()->json(['data' => $this->format($request->user())]);
    }

    private function format(User $user): array
    {
        return [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'role'          => $user->role,
            'profile_image' => $user->profile_image
                ? Storage::disk('public')->url($user->profile_image)
                : null,
            'created_at'    => $user->created_at?->toISOString(),
            'updated_at'    => $user->updated_at?->toISOString(),
        ];
    }
}
