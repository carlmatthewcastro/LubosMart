@extends('layouts.app')
@section('title', 'Chat/Messaging')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Rider</div>
        <a href="{{ route('rider.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('rider.pickups') }}" class="sidebar-link">Available Pickups</a>
        <a href="{{ route('rider.active-delivery') }}" class="sidebar-link">Active Delivery</a>
        <a href="{{ route('rider.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('rider.earnings') }}" class="sidebar-link">Earnings</a>
        <a href="{{ route('rider.chat') }}" class="sidebar-link active">Chat/Messaging</a>
        <a href="{{ route('rider.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('rider.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Chat/Messaging</h1>
        <div class="chat-layout">
            <div class="chat-list">
                <div class="chat-list-item active">
                    <div class="chat-list-name">Anna's Boutique</div>
                    <div class="chat-list-preview">Please knock, gate is locked</div>
                </div>
                <div class="chat-list-item">
                    <div class="chat-list-name">Buyer — Order #VLR-1039</div>
                    <div class="chat-list-preview">Thank you po!</div>
                </div>
                <div class="chat-list-item">
                    <div class="chat-list-name">LUBOSMART Support</div>
                    <div class="chat-list-preview">Your payout has been released</div>
                </div>
            </div>
            <div class="chat-window">
                <div class="chat-header">Anna's Boutique</div>
                <div class="chat-messages">
                    <div class="chat-bubble received">Hi! Order is ready for pickup, packed already.</div>
                    <div class="chat-bubble sent">On my way, ETA 10 mins.</div>
                    <div class="chat-bubble received">Please knock, gate is locked</div>
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