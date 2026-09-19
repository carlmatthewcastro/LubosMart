@extends('layouts.app')
@section('title', 'Choose a Logistics Company')
@section('content')
<div style="min-height:100vh; background:var(--bg); padding:48px 20px;">
    <div class="container-lg" style="margin:0 auto;">
        <h1 style="font-size:26px; font-weight:800; margin-bottom:6px;">You're Approved! 🎉</h1>
        <p style="color:var(--text-gray); margin-bottom:28px;">
            Nearby logistics companies looking for riders will appear here.
            Apply to one to unlock your full dashboard.
        </p>

        <div class="list-grid">
            @foreach (['SPX Express' => '2.3 km away', 'J&T Express' => '3.8 km away', 'Lalamove' => '5.1 km away'] as $company => $distance)
                <div class="list-row">
                    <div>
                        <div class="list-row-title">{{ $company }}</div>
                        <div class="list-row-sub">{{ $distance }} · Hiring</div>
                    </div>
                    <form method="POST" action="{{ route('rider.apply-company') }}">
                        @csrf
                        <input type="hidden" name="company" value="{{ $company }}">
                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection