@extends('layouts.main')

@section('content')

<main>

    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="h2 text-dark mb-1">{{ $isAdmin ? 'Fleet inventory' : 'Available cars' }}</h1>
                <p class="text-muted mb-0">{{ $isAdmin ? 'Manage and search the full vehicle inventory.' : 'Browse and search vehicles currently available to rent.' }}</p>
            </div>
            @unless ($isAdmin)
            <a href="{{ route('booking.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-calendar-plus me-1" aria-hidden="true"></i> Start a booking
            </a>
            @endunless
            @if ($isAdmin)
            <div class="d-flex flex-wrap gap-2">
                <button type="button" id="addCarButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">
                    <i class="fa-solid fa-plus me-1"></i> Add New Car
                </button>
            </div>
            @endif
        </div>

        @if ($isAdmin)
        <div class="row">
            <form id="cars-filter-form" class="col-md-6 d-flex gap-2 mb-4">
                <input type="search" id="search-bar" placeholder="Search all cars by brand or model..." class="form-control" aria-label="Search all cars by brand or model">
                <button type="submit" class="btn btn-primary">Search</button>
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mt-4">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Brand / Model</th>
                        <th>Year</th>
                        <th>Gear Type</th>
                        <th>Status</th>
                        <th>Price/day</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="cars-table-body" data-storage-url="{{ asset('storage') }}" data-csrf-token="{{ csrf_token() }}" data-inventory-url="{{ route('cars.inventory') }}">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Loading cars...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <div class="row mt-4">
            <form id="available-cars-filter-form" class="col-12 col-md-8 col-lg-6 d-flex gap-2">
                <input type="search" id="available-cars-search" placeholder="Search available cars by brand or model..." class="form-control" aria-label="Search available cars by brand or model">
                <button type="submit" class="btn btn-primary">Search</button>
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
            </form>
        </div>
        @endif

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

        @if ($isAdmin)
        <h2 class="h4 mb-3 text-center mt-5">Available cars showcase</h2>
        @endif
        <div class="row loadAvailableCars mt-3" data-storage-url="{{ asset('storage') }}">
            <div class="col-12 text-center text-muted py-4">Loading available cars...</div>
        </div>
    </div>

</main>


@if ($isAdmin)
@include('cars.add_car_modal')
@endif

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

        const carsTableBody = $('#cars-table-body');
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[character]);

        $('#cars-filter-form').on('submit', function(event) {
            event.preventDefault();
            fetchCars({
                search: $('#search-bar').val().trim()
            });
        });

        $('#cars-filter-form').on('reset', function() {
            window.setTimeout(() => fetchCars({}), 0);
        });

        function fetchCars(params) {
            const query = new URLSearchParams(params).toString();

            $.ajax({
                url: `${carsTableBody.data('inventory-url')}${query ? `?${query}` : ''}`,
                method: 'GET',
                success: function(cars) {
                    carsTableBody.empty();

                    if (cars.length === 0) {
                        carsTableBody.append('<tr><td colspan="7" class="text-center text-muted py-4">No cars found.</td></tr>');
                        return;
                    }

                    const storageUrl = carsTableBody.data('storage-url');
                    const csrfToken = escapeHtml(carsTableBody.data('csrf-token'));

                    cars.forEach(car => {
                        const imagePath = car.image ?
                            car.image.split('/').map(encodeURIComponent).join('/') :
                            '';
                        const imageCell = imagePath ?
                            `<img src="${escapeHtml(`${storageUrl}/${imagePath}`)}" alt="${escapeHtml(`${car.brand || 'Car'} ${car.model || ''}`)}" width="72" height="48" class="rounded" style="object-fit: cover;">` :
                            '<span class="text-muted small">No photo</span>';
                        const carId = encodeURIComponent(car.id);

                        carsTableBody.append(`
                            <tr>
                                <td>${imageCell}</td>
                                <td><span class="fw-semibold">${escapeHtml(car.brand)}</span><br><span class="text-muted">${escapeHtml(car.model)}</span></td>
                                <td>${escapeHtml(car.year)}</td>
                                <td>${escapeHtml(car.gear_type)}</td>
                                <td>${escapeHtml(car.status)}</td>
                                <td>Rs${escapeHtml(car.daily_rate)}</td>
                                <td class="text-end text-nowrap">
                                    <a href="/cars/${carId}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="/cars/${carId}" method="POST" class="d-inline" onsubmit="return confirm('Delete this car? Cars with booking history cannot be deleted.')">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        `);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching cars:', error);
                    carsTableBody.html('<tr><td colspan="7" class="text-center text-danger py-4">Unable to load cars. Please refresh and try again.</td></tr>');
                }
            });
        }

        if (carsTableBody.length) {
            fetchCars({});
        }

        $('#available-cars-filter-form').on('submit', function(event) {
            event.preventDefault();
            loadAvailableCars($('#available-cars-search').val().trim());
        });

        $('#available-cars-filter-form').on('reset', function() {
            window.setTimeout(() => loadAvailableCars(), 0);
        });

        function loadAvailableCars(search = '') {
            const loadAvailables = document.querySelector('.loadAvailableCars');
            let availableHTML = "";
            const storageUrl = loadAvailables.dataset.storageUrl;
            const query = new URLSearchParams(search ? { search } : {}).toString();

            $.ajax({
                method: "GET",
                url: '/api/cars' + (query ? `?${query}` : ''),
                success: function(carss) {
                    if (carss.length === 0) {
                        loadAvailables.innerHTML = '<div class="col-12 text-center text-muted py-5">No available cars match your search.</div>';
                        return;
                    }

                    carss.forEach((car) => {
                        const imagePath = car.image ? car.image.split('/').map(encodeURIComponent).join('/') : "";
                        const imageCell = imagePath ?
                            `<img src="${escapeHtml(`${storageUrl}/${imagePath}`)}" alt="${escapeHtml(`${car.brand || "Car"} ${car.model || ""}`)}" class="card-img-top img-fluid" style="height: 180px; object-fit: cover;">` :
                            `<div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 180px;">No photo</div>`;
                        const bookingAction = `<a href="{{ route('booking.create') }}" class="btn btn-primary w-100 rounded-3 fw-semibold">Book this car</a>`;

                        availableHTML += `
                            <div class="col-md-6 col-lg-3 mb-3 mt-2"> 
                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden my-1">
                                ${imageCell}
                                <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                    <h3 class="h5 card-title fw-bold mb-0 text-dark">${escapeHtml(car.brand)}</h3>
                                    <p class="text-muted small mb-2">${escapeHtml(car.model)}</p>
                                    </div>
                                    <div class="text-end">
                                    <span class="fs-5 fw-bold text-primary">Rs ${escapeHtml(car.daily_rate)}</span>
                                    <div class="text-muted" style="font-size: 0.7rem;">per day</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 text-muted small mb-3">
                                    <div><i class="bi bi-person text-secondary"></i> ${escapeHtml(car.seats)} seats</div>
                                    <div><i class="fa-solid fa-gear"></i> ${escapeHtml(car.gear_type)}</div>
                                    <div><i class="fa-solid fa-gas-pump"></i> ${escapeHtml(car.fuel_type)}</div>
                                </div>
                                <div class="mt-auto">${bookingAction}</div>
                                </div>
                            </div>
                            </div>`;
                    });

                    loadAvailables.innerHTML = availableHTML;
                },
                error: function(xhr, status, error) {
                    console.error("Failed to fetch cars:", error);
                    loadAvailables.innerHTML = '<div class="col-12 text-center text-danger py-4">Unable to load available cars. Please refresh and try again.</div>';
                },
            });
        }

        loadAvailableCars();
    });
</script>

@endsection

<script>
    //ALERTS TIMEOUT 
    document.querySelectorAll('.alert').forEach((alertElement) => {
        setTimeout(() => {
            bootstrap.Alert.getOrCreateInstance(alertElement).close();
        }, 3000);
    });
</script>