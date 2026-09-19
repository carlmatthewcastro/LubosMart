@extends('layouts.guest')
@section('title', 'Sign In — LubosMart')

@section('content')

    <div class="login-container">

        {{-- LEFT SIDE --}}
        <div class="login-left">
            <div class="brand">
                <svg class="brand-mark" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="60" height="60" rx="16" fill="#F5A623"/>
                    <path d="M23 25 L25 19 C26 16.3 28 15 32 15 C36 15 38 16.3 39 19 L41 25"
                          stroke="#DB8E10" stroke-width="3" fill="none" stroke-linecap="round"/>
                    <rect x="18" y="24" width="28" height="24" rx="5" fill="#3B1656"/>
                    <circle cx="27.5" cy="34" r="2.4" fill="#F5A623"/>
                    <circle cx="36.5" cy="34" r="2.4" fill="#F5A623"/>
                    <path d="M27 40 Q32 43.5 37 40" stroke="#F5A623" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                    <path d="M23 24 C23 21 26 19.5 32 19.5 C38 19.5 41 21 41 24"
                          stroke="#3B1656" stroke-width="2.6" fill="none" stroke-linecap="round"/>
                </svg>
                <span class="brand-name">LubosMart</span>
            </div>

            <h1>Welcome Back.</h1>

            <p>Sign in to your LubosMart account and continue your shopping journey.</p>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="login-right">
            <h2>Sign In</h2>
            <p class="subtitle">Enter your account details below.</p>

            @if ($errors->any())
                <div class="error-message" style="display:flex;">
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

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="input-group">
                    <label>EMAIL ADDRESS</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="Enter your email" autocomplete="email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>PASSWORD</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" name="password" id="password"
                               placeholder="Enter your password" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Show password">
                            <i class="fa-regular fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>

                <button type="submit" class="login-button">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    LOGIN
                </button>
            </form>

            <p class="register">
                Don't have an account?
                <a href="{{ route('register') }}">Create one</a>
            </p>

            <div class="back-home">
                <a href="{{ route('shop.index') }}">← Back to LubosMart</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

@endsection
