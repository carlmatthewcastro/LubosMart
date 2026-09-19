@extends('layouts.app')
@section('title', 'Super Admin Dashboard')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Admin</div>
                <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link active">Dashboard</a>
        <a href="{{ route('superadmin.buyers') }}" class="sidebar-link">Buyers</a>
        <a href="{{ route('superadmin.sellers') }}" class="sidebar-link">Sellers</a>
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
        <h1 class="page-title">Overview</h1>

        @if (session('status'))
            <p style="color:var(--success-text); background:var(--success-bg); padding:10px 14px; border-radius:8px; font-size:14px; margin-bottom:20px;">{{ session('status') }}</p>
        @endif

        <div class="stats-grid">
            <div class="stat-card"><p>Pending Rider Approvals</p><p>{{ $pending->where('role', 'rider')->count() }}</p></div>
            <div class="stat-card"><p>Pending Logistics Approvals</p><p>{{ $pending->where('role', 'logistics')->count() }}</p></div>
            <div class="stat-card"><p>Total Riders</p><p>{{ $all->where('role', 'rider')->count() }}</p></div>
            <div class="stat-card"><p>Total Logistics Companies</p><p>{{ $all->where('role', 'logistics')->count() }}</p></div>
        </div>

        <div class="panel mt-6">
            <h2 class="panel-title">Pending Applications</h2>
            <table class="table">
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Role</th><th>Date Applied</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($pending as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td style="text-transform:capitalize;">{{ $user->role }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('superadmin.approve', $user) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-link">Approve</button>
                                </form>
                                &nbsp;&nbsp;
                                <form method="POST" action="{{ route('superadmin.reject', $user) }}" style="display:inline;" onsubmit="return confirm('Reject and delete this application?');">
                                    @csrf
                                    <button type="submit" class="btn-danger-link">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="color:var(--text-gray);">No pending applications right now.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel mt-6">
            <h2 class="panel-title">All Riders & Logistics Companies</h2>
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($all as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td style="text-transform:capitalize;">{{ $user->role }}</td>
                            <td>
                                @if ($user->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-success">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="color:var(--text-gray);">No accounts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection