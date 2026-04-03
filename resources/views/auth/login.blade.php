@extends('layouts.guest')

@section('page_title', 'Sign In')

@section('content')
    <main class="guest-shell">
        <div class="guest-grid">
            <section class="login-intro">
                <div class="login-brand">
                    <span class="brand-mark shield-mark">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3 5 6v5c0 4.6 2.9 8 7 9.5 4.1-1.5 7-4.9 7-9.5V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div>
                        <div class="login-brand-title">Finance Control</div>
                        <div class="login-brand-subtitle">Internal Operations Dashboard</div>
                    </div>
                </div>

                <div class="login-copy">
                    <h1 class="hero-title">Financial Data Processing &amp; Access Control</h1>
                    <p class="hero-copy">
                        A comprehensive internal tool for managing financial records with role-based access control. Built for finance teams, analysts, and administrators.
                    </p>
                </div>

                <div class="feature-list">
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <div>
                            <strong>Role-Based Access Control</strong>
                            <div class="muted-text">Viewer, Analyst, and Admin permissions</div>
                        </div>
                    </div>

                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <div>
                            <strong>Financial Record Management</strong>
                            <div class="muted-text">Track income, expenses, and categories</div>
                        </div>
                    </div>

                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <div>
                            <strong>Advanced Filtering &amp; Analytics</strong>
                            <div class="muted-text">Dashboard summaries and detailed reports</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="login-panel">
                <section class="auth-card auth-card-compact">
                    <h2>Sign In</h2>
                    <p class="helper-copy">Enter your credentials to access the dashboard</p>

                    @include('partials.flash')

                    <form class="auth-form" method="POST" action="{{ route('app.login.store') }}">
                        @csrf

                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', 'priya.viewer@financecontrol.in') }}" required autofocus>
                        </div>

                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" name="password" type="password" value="password" required>
                        </div>

                        <label class="checkbox-field">
                            <input name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <span>Remember me</span>
                        </label>

                        <div class="filter-actions filter-actions-stretch">
                            <button class="btn btn-primary btn-block" type="submit">Sign In</button>
                        </div>
                    </form>
                </section>

                <section class="demo-panel">
                    <div class="demo-title">
                        <span class="demo-dot">i</span>
                        <span>Demo Accounts</span>
                    </div>

                    <div class="demo-account-list">
                        <button class="demo-account-card viewer" type="button" data-email="priya.viewer@financecontrol.in" data-password="password">
                            <div>
                                <strong>Viewer</strong>
                                <span>Can view dashboard and records only</span>
                            </div>
                            <span class="demo-action">Click to fill</span>
                        </button>

                        <button class="demo-account-card analyst" type="button" data-email="arjun.analyst@financecontrol.in" data-password="password">
                            <div>
                                <strong>Analyst</strong>
                                <span>Can create, update, and delete records</span>
                            </div>
                            <span class="demo-action">Click to fill</span>
                        </button>

                        <button class="demo-account-card admin" type="button" data-email="kavya.admin@financecontrol.in" data-password="password">
                            <div>
                                <strong>Admin</strong>
                                <span>Full access including user management</span>
                            </div>
                            <span class="demo-action">Click to fill</span>
                        </button>
                    </div>
                </section>
            </section>
        </div>
    </main>

    <script>
        document.querySelectorAll('.demo-account-card').forEach((card) => {
            card.addEventListener('click', () => {
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');

                if (!emailInput || !passwordInput) {
                    return;
                }

                emailInput.value = card.dataset.email || '';
                passwordInput.value = card.dataset.password || '';
            });
        });
    </script>
@endsection
