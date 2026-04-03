@extends('layouts.guest')

@php
    $primaryRoute = auth()->check() ? route('app.dashboard') : route('login');
    $primaryLabel = auth()->check() ? 'Return to Dashboard' : 'Go to Login';
    $previousUrl = url()->previous();
@endphp

@section('page_title', 'Server Error')

@section('content')
    <main class="error-shell">
        <section class="auth-card error-card">
            <div class="error-code">500 Error</div>
            <h1 class="error-title">Something went wrong</h1>
            <p class="error-copy">
                The application hit an unexpected problem. You can go back to the previous page or continue from the main dashboard.
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
