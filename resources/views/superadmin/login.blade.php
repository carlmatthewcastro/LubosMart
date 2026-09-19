@extends('layouts.app')
@section('title', 'Super Admin Login')
@section('content')
<div class="page-center">
    <div class="card container-sm">
        <div class="auth-brand">LUBOSMART</div>
        <p class="subtitle">Super Admin Portal</p>

        @if ($errors->any())
            <p style="color:var(--danger); font-size:13px; margin-bottom:12px;">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('superadmin.login.submit') }}">
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
        </form>
    </div>
</div>
@endsection