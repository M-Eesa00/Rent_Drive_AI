<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $user = null;
        return view('users.create', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.create', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        unset($validated['password_confirmation']);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('users', 'public');
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User added successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        unset($validated['password_confirmation']);

        if ($request->hasFile('image')) {
            $oldImage = $user->image_path;
            $validated['image_path'] = $request->file('image')->store('users', 'public');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->image_path) {
            Storage::disk('public')->delete($user->image_path);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
