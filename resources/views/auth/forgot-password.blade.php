@extends('layouts.guest')
@section('title', 'Forgot Password — LubosMart')

@section('content')

    <div class="login-container">

        {{-- LEFT SIDE --}}
        <div class="login-left">
            @include('auth.partials.brand')

            <h1>Forgot your password?</h1>

            <p>No worries. Enter the email you signed up with and we'll send you a link to choose a new one.</p>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="login-right">
            <h2>Reset Password</h2>
            <p class="subtitle">We'll email you a secure reset link.</p>

            @if ($errors->any())
                <div class="error-message" style="display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="success-message">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="input-group">
                    <label>EMAIL ADDRESS</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="Enter your email" autocomplete="email" required autofocus>
                    </div>
                </div>

                <button type="submit" class="login-button">
                    <i class="fa-regular fa-paper-plane"></i>
                    SEND RESET LINK
                </button>
            </form>

            <p class="register">
                Remembered it?
                <a href="{{ route('login') }}">Back to sign in</a>
            </p>

            <div class="back-home">
                <a href="{{ route('shop.index') }}">← Back to LubosMart</a>
            </div>
        </div>
    </div>

@endsection
