<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Car;

class carsController extends Controller
{
    public function carsList()
    {
        $cars = Car::all();
        return view("cars.cars_list", compact("cars"));
    }

    public function index()
    {
        return response()->json(Car::all());
    }

    public function create()
    {
        $car = null;
        return view('cars.create', compact('car'));
    }

    public function edit(Car $car)
    {
        return view('cars.create', compact('car'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'between:2000,2030'],
            'plate_number' => ['required', 'string', 'max:255', 'unique:cars,plate_number'],
            'totalMileage' => ['required', 'numeric', 'min:0'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'gear_type' => ['required', 'string', 'in:Automatic,Manual'],
            'fuel_type' => ['required', 'string', 'in:Gasoline,Diesel,Electric,Hybrid,Petrol'],
            'seats' => ['required', 'integer', 'between:1,15'],
            'status' => ['required', 'in:available,rented,maintenance'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('cars', 'public');
        }

        Car::create($validated);

        return redirect()->route('cars.cars_list')
            ->with('success', 'Car added successfully.');
    }

    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'between:2000,2030'],
            'plate_number' => ['required', 'string', 'max:255', 'unique:cars,plate_number,' . $car->id],
            'totalMileage' => ['required', 'numeric', 'min:0'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'gear_type' => ['required', 'string', 'in:Automatic,Manual'],
            'fuel_type' => ['required', 'string', 'in:Gasoline,Diesel,Electric,Hybrid,Petrol'],
            'seats' => ['required', 'integer', 'between:1,15'],
            'status' => ['required', 'in:available,rented,maintenance'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $oldImage = $car->image;
            $validated['image'] = $request->file('image')->store('cars', 'public');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $car->update($validated);

        return redirect()->route('cars.cars_list')->with('success', 'Car updated successfully.');
    }

    public function destroy(Car $car)
    {
        if ($car->bookings()->exists()) {
            return redirect()->route('cars.cars_list')
                ->with('error', 'This car has booking history and cannot be deleted.');
        }

        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

        $car->delete();

        return redirect()->route('cars.cars_list')->with('success', 'Car deleted successfully.');
    }
}
