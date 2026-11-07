@extends('layouts.auth')

@section('title', __('Log In') . ' | Car Rental Manager')

@section('content')
<section class="cr-login-shell">
    <div class="cr-login-glow cr-login-glow-left" aria-hidden="true"></div>
    <div class="cr-login-glow cr-login-glow-right" aria-hidden="true"></div>
    <div class="cr-login-content">
        <span class="cr-auth-eyebrow"><i aria-hidden="true"></i> YOUR RENTAL WORKSPACE</span>
        <h1>Welcome <span>back.</span></h1>
        <p class="cr-login-intro">Sign in to pick up right where your team left off.</p>

        <div class="cr-login-card">
            <div class="cr-auth-card-heading">
                <span class="cr-auth-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 3.5 19 6v5.6c0 4.1-2.7 7-7 8.9-4.3-1.9-7-4.8-7-8.9V6l7-2.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span><strong>Log in to your account</strong><small>Your fleet is just a sign-in away.</small></span>
            </div>

            <form method="POST" action="{{ route('login') }}" class="cr-auth-form">
                @csrf

                <div class="cr-auth-field">
                    <label for="email">{{ __('Email Address') }} <span aria-hidden="true">*</span></label>
                    <input id="email" type="email" class="form-control cr-auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="cr-auth-field">
                    <div class="cr-auth-label-row">
                        <label for="password">{{ __('Password') }} <span aria-hidden="true">*</span></label>
                        @if (Route::has('password.request'))
                            <a class="cr-auth-text-link" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                        @endif
                    </div>
                    <input id="password" type="password" class="form-control cr-auth-input @error('password') is-invalid @enderror" name="password" placeholder="Enter your password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="cr-auth-form-options">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                    </div>
                </div>

                <button type="submit" class="cr-auth-submit">{{ __('Log In') }} <span aria-hidden="true">↗</span></button>
            </form>

            <p class="cr-auth-switch">{{ __('New to Car Rental Manager?') }} <a href="{{ route('register') }}">Create your account</a></p>
        </div>

        <p class="cr-login-caption"><span aria-hidden="true">✦</span> A clearer view of your fleet, bookings, and customers.</p>
    </div>
</section>
@endsection
