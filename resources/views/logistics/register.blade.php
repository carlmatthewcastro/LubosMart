@extends('layouts.app')
@section('title', 'Logistics Company Registration')
@section('content')
<div class="page-center">
    <div class="card container-lg">
        <div class="auth-brand">LUBOSMART</div>
        <p class="subtitle">Logistics Company Registration</p>

                            <form method="POST" action="{{ route('logistics.register.submit') }}" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <p style="color:var(--danger); font-size:13px; margin-bottom:12px;">{{ $errors->first() }}</p>
            @endif
            <h2 class="section-title">Company Information</h2>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Company Name *</label>
                    <input type="text" name="name" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Business Email *</label>
                    <input type="email" name="email" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Person *</label>
                    <input type="text" name="contact_person" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact No. *</label>
                    <input type="text" name="contact_no" required class="form-input">
                </div>
            </div>

            <hr class="divider">
            <h2 class="section-title">Business Address</h2>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Province</label>
                    <select name="province" class="form-select"></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Municipality/City</label>
                    <select name="municipality" class="form-select"></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Barangay</label>
                    <select name="barangay" class="form-select"></select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Street / Building No.</label>
                <input type="text" name="street" class="form-input">
            </div>

            <hr class="divider">
            <h2 class="section-title">Business Documents</h2>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Business Permit *</label>
                    <input type="file" name="business_permit" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">DTI/SEC Registration *</label>
                    <input type="file" name="dti_sec_upload" required class="form-input">
                </div>
            </div>

            <hr class="divider">
            <h2 class="section-title">Account Security</h2>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required class="form-input">
                </div>
            </div>

            <p class="helper-text">
                After submitting your registration, please wait for the Super Admin's approval,
                which will be sent to your business email.
            </p>

            <button type="submit" class="btn btn-primary btn-block">Submit Registration</button>

            <p class="text-center mt-4" style="font-size:14px;">
                Already have an account?
                <a href="{{ route('logistics.login') }}" class="btn-link">Login</a>
            </p>
        </form>
    </div>
</div>
@endsection