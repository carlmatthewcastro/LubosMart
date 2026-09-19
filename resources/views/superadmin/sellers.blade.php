@extends('layouts.app')
@section('title', 'Sellers')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Admin</div>
        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('superadmin.buyers') }}" class="sidebar-link">Buyers</a>
        <a href="{{ route('superadmin.sellers') }}" class="sidebar-link active">Sellers</a>
        <a href="{{ route('superadmin.riders') }}" class="sidebar-link">Riders</a>
        <a href="{{ route('superadmin.companies') }}" class="sidebar-link">Logistics Companies</a>
        <a href="{{ route('superadmin.categories') }}" class="sidebar-link">Categories</a>
        <a href="{{ route('superadmin.reports') }}" class="sidebar-link">Reports</a>
        <a href="{{ route('superadmin.settings') }}" class="sidebar-link">Settings</a>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Seller Accounts</h1>
        <div class="panel">
            <table class="table">
                <thead><tr><th>Business Name</th><th>Owner</th><th>Category</th><th>Permit</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <tr><td>Anna's Boutique</td><td>Anna Reyes</td><td>Fashion</td><td><a href="#" class="btn-link">View</a></td><td><span class="badge badge-success">Approved</span></td><td><button class="btn-danger-link">Suspend</button></td></tr>
                    <tr><td>TechHub PH</td><td>Carlo Gomez</td><td>Electronics</td><td><a href="#" class="btn-link">View</a></td><td><span class="badge badge-warning">Pending</span></td><td><button class="btn-link">Approve</button></td></tr>
                    <tr><td>GreenMart Grocery</td><td>Liza Tan</td><td>Groceries</td><td><a href="#" class="btn-link">View</a></td><td><span class="badge badge-success">Approved</span></td><td><button class="btn-danger-link">Suspend</button></td></tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection