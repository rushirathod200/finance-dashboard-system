@extends('layouts.guest')

@php
    $primaryRoute = auth()->check() ? route('app.dashboard') : route('login');
    $primaryLabel = auth()->check() ? 'Return to Dashboard' : 'Go to Login';
    $previousUrl = url()->previous();
@endphp

@section('page_title', 'Session Expired')

@section('content')
    <main class="error-shell">
        <section class="auth-card error-card">
            <div class="error-code">419 Error</div>
            <h1 class="error-title">Your session has expired</h1>
            <p class="error-copy">
                The page took too long or your session token is no longer valid. Sign in again or go back and retry your last action.
            </p>

            <div class="filter-actions" style="justify-content: center;">
                @if ($previousUrl !== url()->current())
                    <a class="btn btn-secondary" href="{{ $previousUrl }}">Go Back</a>
                @endif

                <a class="btn btn-primary" href="{{ $primaryRoute }}">{{ $primaryLabel }}</a>
            </div>
        </section>
    </main>
@endsection
