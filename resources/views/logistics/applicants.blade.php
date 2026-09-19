@extends('layouts.app')
@section('title', 'Rider Applicants')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Logistics</div>
        <a href="{{ route('logistics.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('logistics.applicants') }}" class="sidebar-link active">Rider Applicants</a>
        <a href="{{ route('logistics.riders') }}" class="sidebar-link">Manage Riders</a>
        <a href="{{ route('logistics.deliveries') }}" class="sidebar-link">Active Deliveries</a>
        <a href="{{ route('logistics.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('logistics.reports') }}" class="sidebar-link">Reports</a>
        <a href="{{ route('logistics.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('logistics.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('logistics.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Rider Applicants</h1>
        <div class="panel">
            <table class="table">
                <thead><tr><th>Name</th><th>Vehicle</th><th>Plate No.</th><th>Distance</th><th>Documents</th><th>Action</th></tr></thead>
                <tbody>
                    @foreach ([
                        ['name' => 'Mark Villareal', 'vehicle' => 'Motorcycle', 'plate' => 'NCV 3821', 'dist' => '1.8 km'],
                        ['name' => 'Jerome Santos', 'vehicle' => 'Bicycle', 'plate' => 'N/A', 'dist' => '3.2 km'],
                        ['name' => 'Kevin Cruz', 'vehicle' => 'Car', 'plate' => 'ABC 1234', 'dist' => '5.0 km'],
                    ] as $r)
                        <tr>
                            <td>{{ $r['name'] }}</td>
                            <td>{{ $r['vehicle'] }}</td>
                            <td>{{ $r['plate'] }}</td>
                            <td>{{ $r['dist'] }}</td>
                            <td><a href="#" class="btn-link">View ID/OR-CR</a></td>
                            <td><button class="btn-link">Accept</button>&nbsp;&nbsp;<button class="btn-danger-link">Decline</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection