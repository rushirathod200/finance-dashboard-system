@extends('layouts.guest')

@php
    $primaryRoute = auth()->check() ? route('app.dashboard') : route('login');
    $primaryLabel = auth()->check() ? 'Return to Dashboard' : 'Go to Login';
    $previousUrl = url()->previous();
@endphp

@section('page_title', 'Permission Denied')

@section('content')
    <main class="error-shell">
        <section class="auth-card error-card">
            <div class="error-code">403 Error</div>
            <h1 class="error-title">Permission denied</h1>
            <p class="error-copy">
                This area is restricted by role-based access control. Return to the dashboard or sign in with an account that has the required permissions.
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
