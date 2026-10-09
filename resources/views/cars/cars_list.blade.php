@extends('layouts.main')

@section('content')

<main>

    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="text-dark mb-0">Cars Table</h3>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" id="addCarButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">
                    <i class="fa-solid fa-plus me-1"></i> Add New Car
                </button>
            </div>
        </div>

        // FILTER SEARCH

        
        <div class="table-responsive">
            <table class="table table-hover mt-4">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Brand / Model</th>
                        <th>Year</th>
                        <th>Gear Type</th>
                        <th>Price/day</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cars as $car)
                    <tr>
                        <td>
                            @if ($car->image)
                            <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }} {{ $car->model }}" width="72" height="48" class="rounded" style="object-fit: cover;">
                            @else
                            <span class="text-muted small">No photo</span>
                            @endif
                        </td>
                        <td><span class="fw-semibold">{{ $car->brand }}</span><br><span class="text-muted">{{ $car->model }}</span></td>
                        <td>{{ $car->year }}</td>
                        <td>{{ $car->gear_type }}</td>
                        <td>Rs{{ $car->daily_rate }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('cars.edit', $car) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('cars.destroy', $car) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this car? Cars with booking history cannot be deleted.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">No cars have been added.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger mt-4" role="alert">
            <strong>Unable to save the form.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <h3 class="mb-3 text-center mt-5 mb-2">Available Cars Showcase</h3>
        <div class="row loadAvailableCars " data-storage-url="{{ asset('storage') }}">
        </div>
    </div>

</main>

//FILTER SEARCH

            <form id="cars-filter-form" class="row g-3 mb-4">

                <div class="col-md-3">
                    <label for="filter-name" class="form-label fw-semibold">Brand</label>
                    <input type="text" id="filter-name" name="name" class="form-control" placeholder="Search by Name...">
                </div>
                <div class="col-md-3">
                    <label for="filter-email" class="form-label fw-semibold">Model</label>
                    <input type="text" id="filter-email" name="email" class="form-control" placeholder="Search by Email...">
                </div>
                <div class="col-6 gap-2 mt-5">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary">Reset</a>
                </div>

            </form>

<div class="modal fade" id="addCarModal" tabindex="-1" aria-labelledby="addCarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCarModalLabel">r</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            
            <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_form" value="car">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="carBrand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="carBrand" name="brand" required>
                        </div>
                        <div class="col-md-6">
                            <label for="carModel" class="form-label">Model</label>
                            <input type="text" class="form-control" id="carModel" name="model" required>
                        </div>
                        <div class="col-md-4">
                            <label for="carYear" class="form-label">Year</label>
                            <input type="number" class="form-control" id="carYear" name="year" min="2000" max="2030" required>
                        </div>
                        <div class="col-md-4">
                            <label for="carPlateNumber" class="form-label">Plate Number</label>
                            <input type="text" class="form-control" id="carPlateNumber" name="plate_number" required>
                        </div>
                        <div class="col-md-4">
                            <label for="carMileage" class="form-label">Mileage (km)</label>
                            <input type="number" class="form-control" id="carMileage" name="totalMileage" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label for="carDailyRate" class="form-label">Daily Rate</label>
                            <input type="number" class="form-control" id="carDailyRate" name="daily_rate" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <span class="form-label d-block">Gear Type</span>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="carGearAutomatic" name="gear_type" value="Automatic" required>
                                <label class="form-check-label" for="carGearAutomatic">Automatic</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="carGearManual" name="gear_type" value="Manual" required>
                                <label class="form-check-label" for="carGearManual">Manual</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="carFuelType" class="form-label">Fuel Type</label>
                            <select class="form-select" id="carFuelType" name="fuel_type" required>
                                <option value="" selected disabled>Choose...</option>
                                <option value="Gasoline">Gasoline</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Electric">Electric</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Petrol">Petrol</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="carSeats" class="form-label">Number of Seats</label>
                            <input type="number" class="form-control" id="carSeats" name="seats" min="1" max="15" required>
                        </div>
                        <div class="col-md-6">
                            <label for="carStatus" class="form-label">Status</label>
                            <select class="form-select" id="carStatus" name="status" required>
                                <option value="available" selected>Available</option>
                                <option value="rented">Rented</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="carImage" class="form-label">Car Image</label>
                            <input type="file" class="form-control" id="carImage" name="image" accept=".jpg,.jpeg,.png,.webp,image/*">
                            <div class="form-text">Maximum size: 5 MB.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Save Car</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addCarButton = document.getElementById('addCarButton');
        const addCarModal = document.getElementById('addCarModal');

        if (!addCarButton || !addCarModal) {
            return;
        }

        addCarButton.addEventListener('click', function() {
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(addCarModal).show();
                return;
            }

            addCarModal.classList.add('show');
            addCarModal.style.display = 'block';
            addCarModal.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
        });
    });

    $(document).ready(function() {


        //CARS TABLE

        //CARS CARDS

        function loadAvailableCars() {
            let loadAvailables = document.querySelector(".loadAvailableCars");
            let availableHTML = "";
            const storageUrl = loadAvailables.dataset.storageUrl;

            $.ajax({
                method: "GET",
                url: '/api/cars',
                success: function(carss) {
                    carss.forEach((car) => {
                        const imagePath = car.image ? car.image.split('/').map(encodeURIComponent).join('/') : "";
                        const imageCell = imagePath ?
                            `<img src="${storageUrl}/${imagePath}" alt="${car.brand || "Car"} ${car.model || ""}" class="card-img-top img-fluid" style="height: 180px; object-fit: cover;">` :
                            `<div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 180px;">No photo</div>`;

                        if ((car.status || "").toLowerCase() === "available") {
                            availableHTML += `
                            <div class="col-md-6 col-lg-3 mb-3 mt-2"> 
                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden my-1">
                                
                                <!-- Upper Row: Car Image -->
                                ${imageCell}
                                
                                <!-- Lower Row: Details -->
                                <div class="card-body d-flex flex-column">
                                
                                <!-- Title and Price Row -->
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                    <h5 class="card-title fw-bold mb-0 text-dark">${car.brand ?? ""}</h5>
                                    <p class="text-muted small mb-2">${car.model ?? ""}</p>
                                    </div>
                                    <div class="text-end">
                                    <span class="fs-5 fw-bold text-primary">Rs ${car.daily_rate ?? ""}</span>
                                    <div class="text-muted" style="font-size: 0.7rem;">per day</div>
                                    </div>
                                </div>
                                
                                <!-- Specs Row -->
                                <div class="d-flex align-items-center gap-3 text-muted small mb-3">
                                    <div><i class="bi bi-person text-secondary"></i> ${car.seats ?? ""} seats</div>
                                    <div><i class="fa-solid fa-gear"></i> ${car.gear_type ?? ""}</div>
                                    <div><i class="fa-solid fa-gas-pump"></i> ${car.fuel_type ?? ""}</div>
                                </div>
                                
                                <!-- Feature Badges -->
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                
                                </div>
                                
                                <!-- Book Now Button -->
                                <button type="button"
                                    class="btn btn-primary w-100 rounded-3 fw-semibold car-book-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#bookingModal"
                                    data-car-id="${car.id}"
                                    data-car-name="${car.brand ?? ""} ${car.model ?? ""}"
                                    data-daily-rate="${car.daily_rate ?? 0}">
                                    Book Now
                                </button>
                                
                                </div>
                            </div>
                            </div>`;
                        }
                    });

                    loadAvailables.innerHTML = availableHTML;
                },
                error: function(xhr, status, error) {
                    console.error("Failed to fetch Airtable data:", error);
                },
            });
        }
        ///////

        loadAvailableCars();
    })
</script>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Book a car</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Selected vehicle: <strong id="bookingCarName"></strong></p>
                    <input type="hidden" name="car_id" id="bookingCarId">

                    <div class="mb-3">
                        <label for="customerName" class="form-label">Full name</label>
                        <input type="text" class="form-control" name="customer_name" id="customerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="customer_email" id="customerEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerPhone" class="form-label">Phone number</label>
                        <input type="tel" class="form-control" name="customer_phone" id="customerPhone" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerAddress" class="form-label">Address</label>
                        <textarea class="form-control" name="customer_address" id="customerAddress" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="bookingStartDate" class="form-label">Pick-up date</label>
                        <input type="date" class="form-control" name="start_date" id="bookingStartDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="bookingEndDate" class="form-label">Return date</label>
                        <input type="date" class="form-control" name="end_date" id="bookingEndDate" required>
                    </div>
                    <div class="text-muted small">Daily rate: Rs<span id="bookingDailyRate"></span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.car-book-btn', function() {
        $('#bookingCarId').val($(this).data('car-id'));
        $('#bookingCarName').text($(this).data('car-name'));
        $('#bookingDailyRate').text($(this).data('daily-rate'));
    });

    //ALERTS TIMEOUT 
    document.querySelectorAll('.alert').forEach((alertElement) => {
        setTimeout(() => {
            bootstrap.Alert.getOrCreateInstance(alertElement).close();
        }, 3000);
    });
</script>

@endsection