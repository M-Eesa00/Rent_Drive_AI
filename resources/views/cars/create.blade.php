@extends('layouts.main')

@section('content')

<main class="py-4">
    <div class="modal fade" id="carFormModal" tabindex="-1" aria-labelledby="carFormModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title h5" id="carFormModalLabel">{{ $car ? 'Edit Car' : 'Add New Car' }}</h1>
                    <a href="{{ route('cars.cars_list') }}" class="btn-close" aria-label="Close"></a>
                </div>

                <form action="{{ $car ? route('cars.update', $car) : route('cars.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($car)
                    @method('PUT')
                    @endif

                    <div class="modal-body">
                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                        @endif

                        <div class="row g-3">
                            <!-- Brand -->
                            <div class="col-md-6">
                                <label for="brand" class="form-label fw-semibold">Brand <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand', $car?->brand) }}" required>
                            </div>

                            <!-- Model -->
                            <div class="col-md-6">
                                <label for="model" class="form-label fw-semibold">Model <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="model" name="model" value="{{ old('model', $car?->model) }}" required>
                            </div>

                            <!-- Year -->
                            <div class="col-md-4">
                                <label for="year" class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="year" name="year" value="{{ old('year', $car?->year) }}" min="2000" max="2030" required>
                            </div>

                            <!-- Plate Number -->
                            <div class="col-md-4">
                                <label for="plate_number" class="form-label fw-semibold">Plate Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="plate_number" name="plate_number" value="{{ old('plate_number', $car?->plate_number) }}" required>
                            </div>

                            <!-- Total Mileage -->
                            <div class="col-md-4">
                                <label for="totalMileage" class="form-label fw-semibold">Total Mileage (km) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="totalMileage" name="totalMileage" value="{{ old('totalMileage', $car?->totalMileage) }}" min="0" required>
                            </div>

                            <!-- Daily Rate -->
                            <div class="col-md-4">
                                <label for="daily_rate" class="form-label fw-semibold">Daily Rate ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $car?->daily_rate) }}" min="0" required>
                            </div>

                            <!-- Gear Type -->
                            <div class="col-md-4">
                                <span class="form-label fw-semibold d-block">Gear Type <span class="text-danger">*</span></span>
                                @foreach (['Automatic', 'Manual'] as $gearType)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="gearType{{ $gearType }}" name="gear_type" value="{{ $gearType }}" @checked(old('gear_type', $car?->gear_type) === $gearType) required>
                                    <label class="form-check-label" for="gearType{{ $gearType }}">{{ $gearType }}</label>
                                </div>
                                @endforeach
                            </div>

                            <!-- Fuel Type -->
                            <div class="col-md-4">
                                <label for="fuel_type" class="form-label fw-semibold">Fuel Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="fuel_type" name="fuel_type" required>
                                    <option value="" disabled @selected(!old('fuel_type', $car?->fuel_type))>Choose...</option>
                                    <option value="Gasoline" @selected(old('fuel_type', $car?->fuel_type) === 'Gasoline')>Gasoline</option>
                                    <option value="Diesel" @selected(old('fuel_type', $car?->fuel_type) === 'Diesel')>Diesel</option>
                                    <option value="Electric" @selected(old('fuel_type', $car?->fuel_type) === 'Electric')>Electric</option>
                                    <option value="Hybrid" @selected(old('fuel_type', $car?->fuel_type) === 'Hybrid')>Hybrid</option>
                                </select>
                            </div>

                            <!-- Seats -->
                            <div class="col-md-6">
                                <label for="seats" class="form-label fw-semibold">Number of Seats <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="seats" name="seats" value="{{ old('seats', $car?->seats) }}" min="1" max="15" placeholder="e.g. 5" required>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Initial Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="available" @selected(old('status', $car?->status ?? 'available') === 'available')>Available</option>
                                    <option value="rented" @selected(old('status', $car?->status) === 'rented')>Rented</option>
                                    <option value="maintenance" @selected(old('status', $car?->status) === 'maintenance')>Maintenance</option>
                                </select>
                            </div>

                            <!-- Image Upload -->
                            <div class="col-12">
                                <label for="image" class="form-label fw-semibold">Car Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Upload a clear side or front profile of the vehicle.</div>
                                @if ($car?->image)
                                <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }} {{ $car->model }}" class="mt-2 rounded" width="160">
                                @endif
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
                        <button type="submit" class="btn btn-primary px-4">{{ $car ? 'Update Car' : 'Save Vehicle' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carFormModal = document.getElementById('carFormModal');
        if (carFormModal && window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(carFormModal).show();
        }
    });

    
</script>



@endsection