@extends('layouts.app')
@section('title', 'Categories')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Admin</div>
        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('superadmin.buyers') }}" class="sidebar-link">Buyers</a>
        <a href="{{ route('superadmin.sellers') }}" class="sidebar-link">Sellers</a>
        <a href="{{ route('superadmin.riders') }}" class="sidebar-link">Riders</a>
        <a href="{{ route('superadmin.companies') }}" class="sidebar-link">Logistics Companies</a>
        <a href="{{ route('superadmin.categories') }}" class="sidebar-link active">Categories</a>
        <a href="{{ route('superadmin.reports') }}" class="sidebar-link">Reports</a>
        <a href="{{ route('superadmin.settings') }}" class="sidebar-link">Settings</a>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <div class="flex-between" style="margin-bottom:24px;">
            <h1 class="page-title" style="margin-bottom:0;">Product Categories</h1>
            <button class="btn btn-primary btn-sm">+ Add Category</button>
        </div>

        <div class="category-grid">
            @foreach ([
                ['name' => "Men's Fashion", 'icon' => '👔', 'count' => 1240],
                ['name' => "Women's Fashion", 'icon' => '👗', 'count' => 1890],
                ['name' => 'Electronics', 'icon' => '📱', 'count' => 980],
                ['name' => 'Home & Living', 'icon' => '🛋️', 'count' => 760],
                ['name' => 'Health & Beauty', 'icon' => '💄', 'count' => 640],
                ['name' => 'Groceries', 'icon' => '🛒', 'count' => 1120],
                ['name' => 'Toys & Games', 'icon' => '🧸', 'count' => 310],
                ['name' => 'Sports & Outdoors', 'icon' => '⚽', 'count' => 275],
                ['name' => 'Automotive', 'icon' => '🚗', 'count' => 190],
                ['name' => 'Pet Supplies', 'icon' => '🐾', 'count' => 205],
            ] as $cat)
                <div class="category-card">
                    <div class="dash-category-icon">{{ $cat['icon'] }}</div>
                    <div class="category-name">{{ $cat['name'] }}</div>
                    <div class="category-count">{{ $cat['count'] }} products</div>
                    <div style="margin-top:10px; display:flex; gap:8px; justify-content:center;">
                        <button class="btn-link" style="font-size:12px;">Edit</button>
                        <button class="btn-danger-link" style="font-size:12px;">Archive</button>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
</div>
@endsection