@extends('layouts.guest')

@php
    $primaryRoute = auth()->check() ? route('app.dashboard') : route('login');
    $primaryLabel = auth()->check() ? 'Return to Dashboard' : 'Go to Login';
    $previousUrl = url()->previous();
@endphp

@section('page_title', 'Page Not Found')

@section('content')
    <main class="error-shell">
        <section class="auth-card error-card">
            <div class="error-code">404 Error</div>
            <h1 class="error-title">The page you requested could not be found</h1>
            <p class="error-copy">
                The link may be outdated, or the record may no longer exist. Use the dashboard to continue exploring the application.
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
