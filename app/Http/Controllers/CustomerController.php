<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        $customers = Customer::with('bookings')->latest()->get();

        return view('customers.index', compact('customers'));
    }

    public function apiIndex()
    {
        return response()->json(Customer::with('bookings')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('customers', 'public');
        }

        $customer = Customer::create($validated);

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json($customer, 201);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.create', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
            'phone' => ['required', 'string', 'max:30', 'regex:/^\+92\s3[0-9]{2}-[0-9]{7}$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

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

    public function destroy(Customer $customer)
    {
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
