@extends('layouts.app')
@section('title', 'Rider Registration')
@section('content')
<div class="page-center">
    <div class="card container-lg">
        <div class="auth-brand">LUBOSMART</div>
        <p class="subtitle">Rider Registration</p>

               <form method="POST" action="{{ route('rider.register.submit') }}" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <p style="color:var(--danger); font-size:13px; margin-bottom:12px;">{{ $errors->first() }}</p>
            @endif
            <div class="form-group">
                <label class="form-label">Full Name (matches "First Last") *</label>
                <input type="text" name="name" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password *</label>
                <input type="password" name="password_confirmation" required class="form-input">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Middle Initial</label>
                    <input type="text" name="middle_initial" maxlength="2" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Sex *</label>
                    <select name="sex" required class="form-select">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact No. *</label>
                    <input type="text" name="contact_no" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Birthday *</label>
                    <input type="date" name="birthday" required class="form-input" id="birthday">
                </div>
                <div class="form-group">
                    <label class="form-label">Age</label>
                    <input type="text" name="age" readonly id="age" class="form-input">
                </div>
            </div>

            <hr class="divider">
            <h2 class="section-title">Address</h2>
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
                <label class="form-label">Street / House No.</label>
                <input type="text" name="street" class="form-input">
            </div>

            <hr class="divider">
            <h2 class="section-title">Vehicle Information</h2>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Vehicle Type *</label>
                    <select name="vehicle_type" required class="form-select">
                        <option value="">Select</option>
                        <option value="motorcycle">Motorcycle</option>
                        <option value="bicycle">Bicycle</option>
                        <option value="car">Car</option>
                        <option value="van">Van</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Plate Number *</label>
                    <input type="text" name="plate_number" required class="form-input">
                </div>
            </div>

            <hr class="divider">
            <h2 class="section-title">Document Upload</h2>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Upload ID / Driver's License *</label>
                    <input type="file" name="id_upload" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Upload OR/CR *</label>
                    <input type="file" name="or_cr_upload" required class="form-input">
                </div>
            </div>

            <p class="helper-text">
                After submitting your registration, please wait for the administrator's approval,
                which will be sent to your email.
            </p>

            <button type="submit" class="btn btn-primary btn-block">Submit Registration</button>

            <p class="text-center mt-4" style="font-size:14px;">
                Already have an account?
                <a href="{{ route('rider.login') }}" class="btn-link">Login</a>
            </p>
        </form>
    </div>
</div>

<script>
    document.getElementById('birthday').addEventListener('change', function () {
        const bday = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - bday.getFullYear();
        const m = today.getMonth() - bday.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < bday.getDate())) age--;
        document.getElementById('age').value = age;
    });
</script>
@endsection