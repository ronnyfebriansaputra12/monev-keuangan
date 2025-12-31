<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $role = $request->role;

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('plo_code', 'like', "%{$search}%");
            })
            ->when($role, function ($q) use ($role) {
                $q->where('role', $role);
            })
            ->orderBy('name', 'asc')
            ->get();

        return view('master.users.index', compact('users', 'search', 'role'));
    }

    public function create()
    {
        return view('master.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:PLO,Verifikator,Bendahara,PPK,PPSPM',
            'plo_code' => 'required_if:role,PLO|nullable|string|max:50',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'plo_code' => $request->plo_code,
        ]);

        return redirect()->route('master.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('master.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string',
            'plo_code' => 'nullable|string|max:50',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'plo_code' => $request->plo_code,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('master.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('master.users.index')->with('success', 'User berhasil dihapus.');
    }
}
