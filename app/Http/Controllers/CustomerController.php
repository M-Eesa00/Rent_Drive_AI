<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function create()
    {
        $customer = null;
        return view('customers.create', compact('customer'));
    }

    public function index()
    {
        $customers = User::where('role', 'user')->with('bookings')->latest()->get();

        return view('customers.index', compact('customers'));
    }

    public function apiIndex()
    {
        return response()->json(User::where('role', 'user')->with('bookings')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('customers', 'public');
        }
        unset($validated['password_confirmation'], $validated['image']);
        $validated['role'] = 'user';

        $customer = User::create($validated);

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json($customer, 201);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit(User $customer)
    {
        abort_unless($customer->getAttribute('role') === 'user', 404);

        return view('customers.create', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        abort_unless($customer->getAttribute('role') === 'user', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $customer->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        unset($validated['password_confirmation'], $validated['image']);

        if ($request->hasFile('image')) {
            $oldImage = $customer->image_path;
            $validated['image_path'] = $request->file('image')->store('customers', 'public');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer)
    {
        abort_unless($customer->getAttribute('role') === 'user', 404);

        if ($customer->bookings()->exists()) {
            return redirect()->route('customers.index')
                ->with('error', 'This customer has booking history and cannot be deleted.');
        }

        if ($customer->image_path) {
            Storage::disk('public')->delete($customer->image_path);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
