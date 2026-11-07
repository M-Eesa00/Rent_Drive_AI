@extends('layouts.auth')

@section('title', __('Create Account') . ' | Car Rental Manager')

@section('content')
<section class="cr-register-shell">
    <div class="cr-register-glow" aria-hidden="true"></div>
    <div class="cr-auth-container cr-register-grid">
        <div class="cr-register-intro">
            <span class="cr-auth-eyebrow"><i aria-hidden="true"></i> GET STARTED</span>
            <h1>Make room for<br><span>better rentals.</span></h1>
            <p class="cr-register-lede">Bring the important parts of your rental operation into one more organized workspace.</p>

            <div class="cr-register-benefits">
                <h2>A smoother way to get started</h2>
                <ul>
                    <li><span aria-hidden="true">✓</span> Keep fleet and booking details in one place</li>
                    <li><span aria-hidden="true">✓</span> Find customer information when you need it</li>
                    <li><span aria-hidden="true">✓</span> Get started with your existing account details</li>
                </ul>
            </div>

            <div class="cr-register-aside-note">
                <span class="cr-aside-note-icon" aria-hidden="true">✦</span>
                <span><strong>Your business, in better sync.</strong><small>Explore your workspace once your account is ready.</small></span>
            </div>
        </div>

        <div class="cr-register-card-wrap">
            <div class="cr-register-card">
                <div class="cr-auth-card-heading">
                    <span class="cr-auth-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/><path d="M2.8 19.5a6.2 6.2 0 0 1 12.4 0M16 5.2a3.5 3.5 0 0 1 0 6.6M17 14a5.6 5.6 0 0 1 4.2 5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    </span>
                    <span><strong>Create your account</strong><small>Enter your details to get started.</small></span>
                </div>

                <form method="POST" action="{{ route('register') }}" class="cr-auth-form">
                    @csrf

                    <div class="cr-auth-field">
                        <label for="name">{{ __('Name') }} <span aria-hidden="true">*</span></label>
                        <input id="name" type="text" class="form-control cr-auth-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Your name" required autocomplete="name">

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="cr-auth-field">
                        <label for="email">{{ __('Email Address') }} <span aria-hidden="true">*</span></label>
                        <input id="email" type="email" class="form-control cr-auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autocomplete="email">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="cr-auth-field">
                        <label for="password">{{ __('Password') }} <span aria-hidden="true">*</span></label>
                        <input id="password" type="password" class="form-control cr-auth-input @error('password') is-invalid @enderror" name="password" placeholder="At least 8 characters" required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="cr-auth-field">
                        <label for="password-confirm">{{ __('Confirm Password') }} <span aria-hidden="true">*</span></label>
                        <input id="password-confirm" type="password" class="form-control cr-auth-input" name="password_confirmation" placeholder="Enter your password again" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="cr-auth-submit">{{ __('Create Account') }} <span aria-hidden="true">↗</span></button>
                    <p class="cr-auth-terms">Choose a password with at least 8 characters.</p>
                </form>

                <p class="cr-auth-switch">{{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Log in') }}</a></p>
            </div>
            <div class="cr-register-card-halo" aria-hidden="true"></div>
        </div>
    </div>
</section>
@endsection
