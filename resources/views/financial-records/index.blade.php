@extends('layouts.app')

@php
    $user = auth()->user();
    $canManageRecords = $user->hasRole(\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ANALYST);
    $hasAdvancedFilters = collect([
        'type',
        'category',
        'record_date',
        'date_from',
        'date_to',
        'min_amount',
        'max_amount',
        'sort_by',
        'sort_order',
        'per_page',
    ])->contains(fn ($key) => filled($filters[$key] ?? null));
@endphp

@section('page_title', 'Financial Records')
@section('page_subtitle', 'Manage and review all financial transactions')

@section('page_tools')
    @if ($canManageRecords)
        <a class="btn btn-primary" href="{{ route('app.financial-records.create') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <span>Create Record</span>
        </a>
    @endif
@endsection

@section('content')
    <section class="search-panel">
        <form class="search-form" method="GET" action="{{ route('app.financial-records.index') }}">
            <div class="search-row">
                <div class="search-input-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <input class="search-input" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="Search records...">
                </div>

                <details class="filter-details" @if($hasAdvancedFilters) open @endif>
                    <summary class="filter-summary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 6h16l-6 7v5l-4-2v-3L4 6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                        <span>Show Filters</span>
                    </summary>

                    <div class="filter-body">
                        <div class="filters-inline">
                            <div class="field">
                                <label for="type">Type</label>
                                <select id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="income" @selected(($filters['type'] ?? '') === 'income')>Income</option>
                                    <option value="expense" @selected(($filters['type'] ?? '') === 'expense')>Expense</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="category">Category</label>
                                <input id="category" name="category" type="text" value="{{ $filters['category'] ?? '' }}" placeholder="Investment, Rent...">
                            </div>

                            <div class="field">
                                <label for="record_date">Exact Date</label>
                                <input id="record_date" name="record_date" type="date" value="{{ $filters['record_date'] ?? '' }}">
                            </div>

                            <div class="field">
                                <label for="date_from">Date From</label>
                                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}">
                            </div>

                            <div class="field">
                                <label for="date_to">Date To</label>
                                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}">
                            </div>

                            <div class="field">
                                <label for="min_amount">Min Amount</label>
                                <input id="min_amount" name="min_amount" type="number" min="0" step="0.01" value="{{ $filters['min_amount'] ?? '' }}">
                            </div>

                            <div class="field">
                                <label for="max_amount">Max Amount</label>
                                <input id="max_amount" name="max_amount" type="number" min="0" step="0.01" value="{{ $filters['max_amount'] ?? '' }}">
                            </div>

                            <div class="field">
                                <label for="sort_by">Sort By</label>
                                <select id="sort_by" name="sort_by">
                                    <option value="record_date" @selected(($filters['sort_by'] ?? 'record_date') === 'record_date')>Date</option>
                                    <option value="title" @selected(($filters['sort_by'] ?? '') === 'title')>Title</option>
                                    <option value="amount" @selected(($filters['sort_by'] ?? '') === 'amount')>Amount</option>
                                    <option value="category" @selected(($filters['sort_by'] ?? '') === 'category')>Category</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="sort_order">Sort Order</label>
                                <select id="sort_order" name="sort_order">
                                    <option value="desc" @selected(($filters['sort_order'] ?? 'desc') === 'desc')>Descending</option>
                                    <option value="asc" @selected(($filters['sort_order'] ?? '') === 'asc')>Ascending</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="per_page">Rows Per Page</label>
                                <select id="per_page" name="per_page">
                                    @foreach ([10, 15, 25, 50] as $size)
                                        <option value="{{ $size }}" @selected((int) ($filters['per_page'] ?? 10) === $size)>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="filter-actions">
                            <button class="btn btn-primary" type="submit">Apply</button>
                            <a class="btn btn-secondary" href="{{ route('app.financial-records.index') }}">Reset</a>
                        </div>
                    </div>
                </details>
            </div>
        </form>
    </section>

    <p class="table-meta">
        Showing {{ $records->firstItem() ?? 0 }}-{{ $records->lastItem() ?? 0 }} of {{ $records->total() }} records
    </p>

    <section class="table-card">
        @if ($records->count() === 0)
            <div class="empty-card">No financial records match the current filters.</div>
        @else
            <div class="table-wrap desktop-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Owner</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                            <tr>
                                <td>
                                    <div class="table-title">
                                        <strong><a href="{{ route('app.financial-records.show', $record) }}">{{ $record->title }}</a></strong>
                                        <span class="table-desc">{{ $record->description ?: 'No description provided.' }}</span>
                                    </div>
                                </td>
                                <td><span class="type-pill {{ $record->type }}">{{ $record->type }}</span></td>
                                <td>{{ $record->category }}</td>
                                <td><span class="table-number {{ $record->type }}">{{ \App\Support\Currency::inr($record->amount) }}</span></td>
                                <td>{{ $record->record_date->format('d/m/Y') }}</td>
                                <td>{{ $record->creator?->name ?? 'Unknown' }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a class="icon-button" href="{{ route('app.financial-records.show', $record) }}" title="View">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" stroke="currentColor" stroke-width="1.8"/>
                                                <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                            </svg>
                                        </a>

                                        @if ($canManageRecords)
                                            <a class="icon-button" href="{{ route('app.financial-records.edit', $record) }}" title="Edit">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 20h4l10-10-4-4L4 16v4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                    <path d="m12 6 4 4" stroke="currentColor" stroke-width="1.8"/>
                                                </svg>
                                            </a>

                                            <form method="POST" action="{{ route('app.financial-records.destroy', $record) }}" onsubmit="return confirm('Delete this financial record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="icon-button danger" type="submit" title="Delete">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M5 7h14M10 11v6M14 11v6M7 7l1 12h8l1-12M9 7V4h6v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
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
                @foreach ($records as $record)
                    <article class="mobile-row">
                        <div class="mobile-row-head">
                            <div class="table-title">
                                <strong><a href="{{ route('app.financial-records.show', $record) }}">{{ $record->title }}</a></strong>
                                <span class="table-desc">{{ $record->description ?: 'No description provided.' }}</span>
                            </div>
                            <span class="type-pill {{ $record->type }}">{{ $record->type }}</span>
                        </div>

                        <div class="mobile-row-meta">
                            <span>{{ $record->category }}</span>
                            <span>{{ $record->record_date->format('d/m/Y') }}</span>
                            <span>{{ $record->creator?->name ?? 'Unknown' }}</span>
                        </div>

                        <div class="mobile-row-actions">
                            <a class="btn btn-secondary" href="{{ route('app.financial-records.show', $record) }}">View</a>

                            @if ($canManageRecords)
                                <a class="btn btn-ghost" href="{{ route('app.financial-records.edit', $record) }}">Edit</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        @include('partials.pagination', ['paginator' => $records])
    </section>
@endsection
