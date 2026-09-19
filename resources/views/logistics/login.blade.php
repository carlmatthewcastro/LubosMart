@extends('layouts.app')
@section('title', 'Logistics Company Login')
@section('content')
<div class="page-center">
    <div class="card container-sm">
        <div class="auth-brand">LUBOSMART</div>
        <p class="subtitle">Logistics Company Login</p>

        @if (session('status'))
            <p style="color:var(--success-text); background:var(--success-bg); padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px;">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p style="color:var(--danger); font-size:13px; margin-bottom:12px;">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('logistics.login.submit') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" required class="form-input">
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-4">Login</button>
            <p class="text-center mt-4" style="font-size:14px;">
                Don't have an account?
                <a href="{{ route('logistics.register') }}" class="btn-link">Register</a>
            </p>
        </form>
    </div>
</div>
@endsection