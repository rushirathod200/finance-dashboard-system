@extends('layouts.app')

@section('page_title', $isEdit ? 'Edit User' : 'Create User')
@section('page_subtitle', $isEdit ? 'Update role, status, and account details' : 'Set up a new account with the correct access level')

@section('page_tools')
    <a class="btn btn-secondary" href="{{ route('app.users.index') }}">Back to Users</a>
@endsection

@section('content')
    <section class="form-layout">
        <form class="form-card" method="POST" action="{{ $isEdit ? route('app.users.update', $managedUser) : route('app.users.store') }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="card-head">
                <h2 class="card-title">{{ $isEdit ? 'Update User Details' : 'New User Account' }}</h2>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="name">Full Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $managedUser->name) }}" required>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $managedUser->email) }}" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" {{ $isEdit ? '' : 'required' }}>
                    <span class="field-hint">{{ $isEdit ? 'Leave blank to keep the current password.' : 'Use at least 8 characters.' }}</span>
                </div>

                <div class="field">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="viewer" @selected(old('role', $managedUser->role) === 'viewer')>Viewer</option>
                        <option value="analyst" @selected(old('role', $managedUser->role) === 'analyst')>Analyst</option>
                        <option value="admin" @selected(old('role', $managedUser->role) === 'admin')>Admin</option>
                    </select>
                </div>

                <div class="field">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active" required>
                        <option value="1" @selected((string) old('is_active', $managedUser->is_active ? '1' : '0') === '1')>Active</option>
                        <option value="0" @selected((string) old('is_active', $managedUser->is_active ? '1' : '0') === '0')>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Save User' : 'Create User' }}</button>
                <a class="btn btn-secondary" href="{{ route('app.users.index') }}">Cancel</a>
            </div>
        </form>

        <aside class="info-card">
            <div class="card-head">
                <h2 class="card-title">Role Guide</h2>
            </div>

            <ul class="info-list">
                <li><strong>Viewer:</strong> read-only access to dashboard and records.</li>
                <li><strong>Analyst:</strong> can create, update, and delete financial records.</li>
                <li><strong>Admin:</strong> can manage users and all financial records.</li>
            </ul>

            @if ($isEdit)
                <div class="detail-item" style="margin-top: 18px;">
                    <span class="detail-label">Current Status</span>
                    <span class="detail-value">{{ $managedUser->is_active ? 'Active account' : 'Inactive account' }}</span>
                </div>
            @endif
        </aside>
    </section>
@endsection
