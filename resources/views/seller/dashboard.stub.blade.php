@extends('layouts.shop')
@section('title', 'Seller Dashboard — LubosMart')
@section('styles')
    .stub-wrap { min-height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 40px; }
    .stub-wrap h1 { color: #5B2A86; margin-bottom: 10px; }
    .stub-wrap p { color: #77717D; max-width: 400px; }
@endsection
@section('content')
<div class="stub-wrap">
    <div>
        <h1>Welcome, {{ auth()->user()->name }} 👋</h1>
        <p>Your full Seller Dashboard (inventory, orders, reports) is coming in the next build phase. You're logged in and approved as a Seller.</p>
    </div>
</div>
@endsection