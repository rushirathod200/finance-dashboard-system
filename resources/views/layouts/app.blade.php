<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('page_title', 'Finance Control') · Finance Control</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('app-ui.css') }}">
    </head>
    <body class="app-body">
        @php($user = auth()->user())

        <div class="app-shell">
            <aside class="app-sidebar">
                <a class="brand-link" href="{{ route('app.dashboard') }}">
                    <span class="brand-mark">FC</span>
                    <span class="brand-name">Finance Control</span>
                </a>

                <div class="sidebar-profile">
                    <span class="profile-avatar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M5 20a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>

                    <div class="profile-meta">
                        <span class="profile-name">{{ $user->name }}</span>
                        <span class="role-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <a class="sidebar-link {{ request()->routeIs('app.dashboard') ? 'is-active' : '' }}" href="{{ route('app.dashboard') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="4" y="4" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.8"/>
                            <rect x="14" y="4" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.8"/>
                            <rect x="4" y="14" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.8"/>
                            <rect x="14" y="14" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a class="sidebar-link {{ request()->routeIs('app.financial-records.*') ? 'is-active' : '' }}" href="{{ route('app.financial-records.index') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 4h7l5 5v9a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M14 4v5h5" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M9 13h6M9 17h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <span>Financial Records</span>
                    </a>

                    @if ($user->hasRole(\App\Models\User::ROLE_ADMIN))
                        <a class="sidebar-link {{ request()->routeIs('app.users.*') ? 'is-active' : '' }}" href="{{ route('app.users.index') }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M20 8v6M23 11h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            <span>User Management</span>
                        </a>
                    @endif
                </nav>

                <div class="sidebar-spacer"></div>

                <form class="logout-form" method="POST" action="{{ route('app.logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M16 17l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 12H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </aside>

            <main class="app-main">
                <header class="page-header">
                    <div>
                        <h1 class="page-title">@yield('page_title')</h1>
                        <p class="page-subtitle">@yield('page_subtitle')</p>
                    </div>

                    <div class="page-tools">
                        @yield('page_tools')
                    </div>
                </header>

                @include('partials.flash')

                @yield('content')
            </main>
        </div>

        @include('partials.flash-script')
    </body>
</html>
