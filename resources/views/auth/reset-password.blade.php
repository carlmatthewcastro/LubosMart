@extends('layouts.guest')
@section('title', 'Choose a New Password — LubosMart')

@section('content')

    <div class="login-container">

        {{-- LEFT SIDE --}}
        <div class="login-left">
            @include('auth.partials.brand')

            <h1>Almost there.</h1>

            <p>Choose a new password for your LubosMart account. Make it something you'll remember.</p>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="login-right">
            <h2>New Password</h2>
            <p class="subtitle">Enter and confirm your new password below.</p>

            @if ($errors->any())
                <div class="error-message" style="display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-group">
                    <label>EMAIL ADDRESS</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" name="email" value="{{ old('email', $email) }}"
                               placeholder="Enter your email" autocomplete="email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>NEW PASSWORD</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" name="password" id="password"
                               placeholder="At least 6 characters" autocomplete="new-password" required autofocus>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'passwordIcon')" aria-label="Show password">
                            <i class="fa-regular fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="input-group">
                    <label>CONFIRM NEW PASSWORD</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" name="password_confirmation" id="confirmPassword"
                               placeholder="Type it again" autocomplete="new-password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', 'confirmPasswordIcon')" aria-label="Show password">
                            <i class="fa-regular fa-eye" id="confirmPasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-button">
                    <i class="fa-solid fa-key"></i>
                    RESET PASSWORD
                </button>
            </form>

            <p class="register">
                Link not working?
                <a href="{{ route('password.request') }}">Request a new one</a>
            </p>
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
