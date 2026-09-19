@extends('layouts.app')
@section('title', 'Pending Approval')
@section('content')
<div class="page-center">
    <div class="card container-sm text-center">
        <div class="pill-badge">Application Status</div>
        <h1 style="font-size:20px; font-weight:800; margin-bottom:10px;">Application Under Review</h1>
        <p style="color:var(--text-gray); font-size:14px; margin-bottom:24px;">
            Your rider application is currently being reviewed by the Super Admin.
            You'll receive an email once your account has been approved.
        </p>
        <form method="POST" action="{{ route('rider.logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-block">Logout</button>
        </form>
    </div>
</div>
@endsection