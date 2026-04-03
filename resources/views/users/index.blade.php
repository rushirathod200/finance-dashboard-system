@extends('layouts.app')

@section('page_title', 'User Management')
@section('page_subtitle', 'Manage user accounts and access permissions')

@section('page_tools')
    <a class="btn btn-primary" href="{{ route('app.users.create') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        <span>Create User</span>
    </a>
@endsection

@section('content')
    <section class="admin-banner">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3 4 7v5c0 5 3.4 8.6 8 10 4.6-1.4 8-5 8-10V7l-8-4Z" stroke="#f0a53b" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M12 10v4M12 17h.01" stroke="#f0a53b" stroke-width="1.8" stroke-linecap="round"/>
        </svg>

        <div>
            <strong>Admin Access Required</strong>
            <div class="card-copy">This area is restricted to administrators only. You have full control over user accounts, roles, and permissions.</div>
        </div>
    </section>

    <section class="search-panel users-search-panel" style="margin-top: 18px;">
        <form class="users-filter-row" method="GET" action="{{ route('app.users.index') }}">
            <div class="search-input-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <input id="search" class="search-input" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="Search users..." aria-label="Search users">
            </div>

            <div class="field-inline">
                <select id="role" name="role" aria-label="Filter by role" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="viewer" @selected(($filters['role'] ?? '') === 'viewer')>Viewer</option>
                    <option value="analyst" @selected(($filters['role'] ?? '') === 'analyst')>Analyst</option>
                    <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Admin</option>
                </select>
            </div>

            <div class="field-inline">
                <select id="status" name="status" aria-label="Filter by status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
            </div>

            @if (filled($filters['search'] ?? null) || filled($filters['role'] ?? null) || filled($filters['status'] ?? null))
                <a class="btn btn-secondary" href="{{ route('app.users.index') }}">Reset</a>
            @endif

            <button class="sr-only-submit" type="submit">Search</button>
        </form>
    </section>

    <p class="table-meta">
        Showing {{ $users->count() }} of {{ $users->total() }} users
    </p>

    <section class="table-card">
        @if ($users->count() === 0)
            <div class="empty-card">No users match the current filters.</div>
        @else
            <div class="table-wrap desktop-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $managedUser)
                            <tr>
                                <td>
                                    <div class="table-user-cell">
                                        <span class="table-avatar">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                                                <path d="M5 20a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                        </span>

                                        <div class="table-user-meta">
                                            <strong class="table-user-name">{{ $managedUser->name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $managedUser->email }}</td>
                                <td><span class="role-badge {{ $managedUser->role }}">{{ ucfirst($managedUser->role) }}</span></td>
                                <td><span class="status-pill {{ $managedUser->is_active ? 'active' : 'inactive' }}">{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>{{ $managedUser->created_at?->format('d/m/Y') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a class="icon-button" href="{{ route('app.users.edit', $managedUser) }}" title="Edit">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                <path d="m12 6 4 4" stroke="currentColor" stroke-width="1.8"/>
                                            </svg>
                                        </a>

                                        @if ($managedUser->is_active && ! auth()->user()->is($managedUser))
                                            <form method="POST" action="{{ route('app.users.destroy', $managedUser) }}" onsubmit="return confirm('Deactivate this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="icon-button danger" type="submit" title="Deactivate">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="m18 8 4 4m0-4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mobile-list">
                @foreach ($users as $managedUser)
                    <article class="mobile-row">
                        <div class="mobile-row-head">
                            <div class="table-title">
                                <strong>{{ $managedUser->name }}</strong>
                                <span class="table-desc">{{ $managedUser->email }}</span>
                            </div>

                            <span class="role-badge {{ $managedUser->role }}">{{ ucfirst($managedUser->role) }}</span>
                        </div>

                        <div class="mobile-row-meta">
                            <span>{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span>
                            <span>{{ $managedUser->created_at?->format('d/m/Y') }}</span>
                        </div>

                        <div class="mobile-row-actions">
                            <a class="btn btn-secondary" href="{{ route('app.users.edit', $managedUser) }}">Edit</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        @include('partials.pagination', ['paginator' => $users])
    </section>
@endsection
