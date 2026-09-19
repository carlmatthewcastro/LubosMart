@extends('layouts.app')
@section('title', 'LubosMart Logistics Portal')
@section('content')
<div style="min-height:100vh; background:var(--bg); padding:64px 20px;">
    <div class="container-lg" style="margin:0 auto; text-align:center;">
        <div class="pill-badge">LUBOSMART LOGISTICS NETWORK</div>
        <h1 style="font-size:34px; font-weight:800; margin-bottom:12px;">
            Welcome to <span style="color:var(--purple);">LubosMart</span> Logistics
        </h1>
        <p style="color:var(--text-gray); font-size:15px; margin-bottom:40px; max-width:520px; margin-left:auto; margin-right:auto;">
            Choose your portal to continue.
        </p>

        <div class="list-grid" style="text-align:left;">
            <div class="list-row">
                <div>
                    <div class="list-row-title">Rider Portal</div>
                    <div class="list-row-sub">Deliver orders and manage your earnings</div>
                </div>
                <a href="{{ route('rider.login') }}" class="btn btn-primary btn-sm">Continue</a>
            </div>

            <div class="list-row">
                <div>
                    <div class="list-row-title">Logistics Company Portal</div>
                    <div class="list-row-sub">Manage your fleet of riders and deliveries</div>
                </div>
                <a href="{{ route('logistics.login') }}" class="btn btn-primary btn-sm">Continue</a>
            </div>

            <div class="list-row">
                <div>
                    <div class="list-row-title">Super Admin Portal</div>
                    <div class="list-row-sub">Platform-wide oversight and approvals</div>
                </div>
                <a href="{{ route('superadmin.login') }}" class="btn btn-primary btn-sm">Continue</a>
            </div>
        </div>
    </div>
</div>
@endsection