@extends('layouts.app')
@section('title', 'Riders')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Admin</div>
        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('superadmin.buyers') }}" class="sidebar-link">Buyers</a>
        <a href="{{ route('superadmin.sellers') }}" class="sidebar-link">Sellers</a>
        <a href="{{ route('superadmin.riders') }}" class="sidebar-link active">Riders</a>
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
        <h1 class="page-title">All Riders</h1>
        <div class="panel">
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Company</th><th>Vehicle</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($all->where('role', 'rider') ?? [] as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>—</td>
                            <td>—</td>
                            <td>
                                @if ($user->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-success">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="color:var(--text-gray);">No riders registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection