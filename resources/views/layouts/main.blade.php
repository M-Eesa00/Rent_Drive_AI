<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link
        rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />


</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav
            class="navbar navbar-expand-lg fw-bolder text-dark fixed-top"
            style="background-color: lightgray">
            <div class="container text-dark">
                <a class="navbar-brand" href="#">Car Rental</a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" aria-current="page" href="{{ route('dashboard') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('bookings') ? 'active' : '' }}" href="{{ route('show_booking_list') }}">Bookings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('cars') ? 'active' : '' }}" href="{{ route('cars.cars_list') }}">Cars</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}">Customers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a>
                        </li>

                        <li class="nav-item dropdown ms-3">
                            <button
                                class="btn btn-outline-light btn-sm rounded-circle dropdown-toggle"
                                type="button"
                                id="profileDropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                style="width: 50px; height: 50px; padding: 0">
                                <span class="fw-bold">More</span>
                            </button>
                            <ul
                                class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="profileDropdown">
                                <li>
                                    <h6 class="dropdown-header">Instructor</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#contact">My Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li><a class="dropdown-item" href="{{ asset('logout') }}">Sign Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container flex-grow-1" style="padding-top: 90px">
        @yield('content')
    </main>

    <footer class="mt-auto border-top bg-dark text-light">
        <div class="container py-4">
            <div class="row gy-3 align-items-center">
                <div class="col-12 col-md-5 text-center text-md-start">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-dark fw-semibold">
                        <i class="fa-solid fa-car-side text-primary me-2" aria-hidden="true"></i>Car Rental
                    </a>
                    <p class="text-secondary small mb-0 mt-1">Car Rental Management System</p>
                </div>
                <nav class="col-12 col-md-7" aria-label="Footer navigation">
                    <ul class="nav justify-content-center justify-content-md-end gap-1">
                        <li class="nav-item"><a class="nav-link py-1 px-2 text-secondary" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link py-1 px-2 text-secondary" href="{{ route('show_booking_list') }}">Bookings</a></li>
                        <li class="nav-item"><a class="nav-link py-1 px-2 text-secondary" href="{{ route('cars.cars_list') }}">Cars</a></li>
                        <li class="nav-item"><a class="nav-link py-1 px-2 text-secondary" href="{{ route('customers.index') }}">Customers</a></li>
                    </ul>
                </nav>
                <div class="col-12 text-center text-secondary small pt-2 border-top">
                    &copy; {{ now()->year }} Car Rental Management System. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    @yield('scripts')

</body>

</html>