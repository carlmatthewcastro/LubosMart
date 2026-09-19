@extends('layouts.guest')
@section('title', 'Create Account — LubosMart')

@section('content')

    <div class="register-container">

        {{-- BRAND SIDE --}}
        <div class="brand-section">
            <div class="brand-content">
                <div class="brand-logo">
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
                    <h1>LubosMart</h1>
                </div>

                <p>Join LubosMart and discover a better way to shop, sell, and connect.</p>
            </div>
        </div>

        {{-- REGISTER FORM --}}
        <div class="form-section">
            <h2>Create your account</h2>
            <p class="subtitle">Enter your details to get started.</p>

            @if ($errors->any())
                <div class="form-error" style="display:block;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf

                <div class="input-row">
                    <div class="input-group">
                        <label>FIRST NAME</label>
                        <div class="input-box">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First name" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>LAST NAME</label>
                        <div class="input-box">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last name" required>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label>EMAIL ADDRESS</label>
                    <div class="input-box">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>PASSWORD</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" placeholder="Create a password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'passwordIcon')">
                            <i class="fa-regular fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="input-group">
                    <label>CONFIRM PASSWORD</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirm your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', 'confirmPasswordIcon')">
                            <i class="fa-regular fa-eye" id="confirmPasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="account-title">ACCOUNT TYPE</div>

                <div class="account-types">
                    <label class="account-type">
                        <input type="radio" name="account_type" value="buyer" {{ old('account_type', 'buyer') === 'buyer' ? 'checked' : '' }} required>
                        Buyer
                    </label>

                    <label class="account-type">
                        <input type="radio" name="account_type" value="seller" {{ old('account_type') === 'seller' ? 'checked' : '' }}>
                        Seller
                    </label>
                </div>

                <button type="submit" class="register-button">CREATE ACCOUNT</button>
            </form>

            <div class="login-text">
                Already have an account?
                <a href="{{ route('login') }}">Sign in</a>
            </div>

            <div class="back-home">
                <a href="{{ route('shop.index') }}">← Back to LubosMart</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

@endsection
