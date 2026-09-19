@extends('layouts.app')
@section('title', 'Chat/Messaging')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Logistics</div>
        <a href="{{ route('logistics.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('logistics.applicants') }}" class="sidebar-link">Rider Applicants</a>
        <a href="{{ route('logistics.riders') }}" class="sidebar-link">Manage Riders</a>
        <a href="{{ route('logistics.deliveries') }}" class="sidebar-link">Active Deliveries</a>
        <a href="{{ route('logistics.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('logistics.reports') }}" class="sidebar-link">Reports</a>
        <a href="{{ route('logistics.chat') }}" class="sidebar-link active">Chat/Messaging</a>
        <a href="{{ route('logistics.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('logistics.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Chat/Messaging</h1>
        <div class="chat-layout">
            <div class="chat-list">
                <div class="chat-list-item active">
                    <div class="chat-list-name">Mark Villareal (Rider)</div>
                    <div class="chat-list-preview">On the way to pickup</div>
                </div>
                <div class="chat-list-item">
                    <div class="chat-list-name">LUBOSMART Support</div>
                    <div class="chat-list-preview">New rider applications pending</div>
                </div>
            </div>
            <div class="chat-window">
                <div class="chat-header">Mark Villareal</div>
                <div class="chat-messages">
                    <div class="chat-bubble received">Good morning po, on my way to first pickup.</div>
                    <div class="chat-bubble sent">Noted, drive safe!</div>
                </div>
                <div class="chat-input-row">
                    <input type="text" class="form-input" placeholder="Type a message...">
                    <button class="btn btn-primary">Send</button>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection