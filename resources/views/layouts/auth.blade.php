<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0a0c">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Car Rental Manager')</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/auth-style.css') }}">
</head>
<body class="cr-auth-page">
    <header class="cr-auth-header">
        <nav class="cr-auth-nav cr-auth-container" aria-label="Main navigation">
            <a class="cr-auth-brand" href="{{ url('/') }}" aria-label="Car Rental Manager home">
                <span class="cr-auth-brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 13.5 5.7 8.8A2 2 0 0 1 7.6 7.5h8.8a2 2 0 0 1 1.9 1.3l1.7 4.7v4a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1V17h-9v.5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="M5 13h14M7.5 10.5h9M7 15.5h.01M17 15.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </span>
                <span>Car Rental <strong>Manager</strong></span>
            </a>

            <button class="cr-auth-menu-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#cr-auth-navigation" aria-controls="cr-auth-navigation" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span></span><span></span>
            </button>

            <div class="collapse cr-auth-navigation" id="cr-auth-navigation">
                <div class="cr-auth-nav-links">
                    <a href="{{ route('cars.cars_list') }}">Fleet</a>
                    <a href="{{ url('/#features') }}">Features</a>
                    <a href="{{ url('/#pricing') }}">Pricing</a>
                    <a href="{{ url('/#testimonials') }}">Testimonials</a>
                </div>
                <div class="cr-auth-nav-actions">
                    <a class="cr-auth-login-link" href="{{ route('login') }}">Log In</a>
                    <a class="cr-auth-cta" href="{{ route('booking.create') }}">Book a Car <span aria-hidden="true">↗</span></a>
                </div>
            </div>
        </nav>
    </header>

    <main class="cr-auth-main">
        @yield('content')
    </main>

    <footer class="cr-auth-footer">
        <div class="cr-auth-container cr-auth-footer-inner">
            <a href="{{ url('/') }}">Car Rental <strong>Manager</strong></a>
            <span>Built for smoother rental days.</span>
            <a href="{{ route('cars.cars_list') }}">Explore the fleet <span aria-hidden="true">↗</span></a>
        </div>
    </footer>
</body>
</html>
