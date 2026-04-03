@extends('layouts.app')

@php
    $user = auth()->user();
    $canManageRecords = $user->hasRole(\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ANALYST);
    $roleConfig = [
        'admin' => [
            'label' => 'Admin Role',
            'class' => 'admin',
            'permissions' => ['View dashboard & records', 'Create & edit records', 'Manage users'],
        ],
        'analyst' => [
            'label' => 'Analyst Role',
            'class' => 'analyst',
            'permissions' => ['View dashboard & records', 'Create & edit records'],
        ],
        'viewer' => [
            'label' => 'Viewer Role',
            'class' => 'viewer',
            'permissions' => ['View dashboard & records'],
        ],
    ][$user->role];

    $periodOptions = [
        'last_7_days' => 'Last 7 days',
        'last_30_days' => 'Last 30 days',
        'last_90_days' => 'Last 90 days',
        'all_time' => 'All time',
    ];

    $incomeGroups = collect($summary['income_by_category'])->sortByDesc(fn ($item) => (float) $item['total'])->take(3)->values();
    $incomePalette = ['#4f9d94', '#5ebe86', '#f0b24d'];
    $positions = ['top-left', 'bottom-right', 'right'];
    $incomeTotal = max((float) $summary['total_income'], 1);
    $segments = [];
    $offset = 0;

    foreach ($incomeGroups as $index => $group) {
        $percent = round(((float) $group['total'] / $incomeTotal) * 100, 1);
        $segments[] = "{$incomePalette[$index]} {$offset}% ".($offset + $percent).'%';
        $offset += $percent;
    }

    if ($offset < 100) {
        $segments[] = '#eef2ef '.$offset.'% 100%';
    }

    $pieStyle = 'background: conic-gradient('.implode(', ', $segments).');';

    $expenseLookup = collect($summary['expense_by_category'])->mapWithKeys(fn ($item) => [
        strtolower($item['category']) => (float) $item['total'],
    ]);
    $expenseGroups = collect(['Rent', 'Groceries', 'Utilities', 'Transport', 'Healthcare', 'Entertainment'])
        ->map(fn ($category) => [
            'category' => $category,
            'total' => (float) ($expenseLookup[strtolower($category)] ?? 0),
        ])
        ->filter(fn ($item) => $item['total'] > 0)
        ->values();
    $maxExpense = max((float) $expenseGroups->max('total'), 1);
    $expenseAxisMax = (int) ceil($maxExpense / 550) * 550;
    $expenseAxisSteps = collect(range(4, 0))->map(fn ($step) => (int) (($expenseAxisMax / 4) * $step));
@endphp

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview of your financial data and activities')

@section('page_tools')
    <form class="toolbar-inline" method="GET" action="{{ route('app.dashboard') }}">
        <select class="toolbar-select" name="period" onchange="this.form.submit()">
            @foreach ($periodOptions as $value => $label)
                <option value="{{ $value }}" @selected($selectedPeriod === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <span class="role-badge {{ $roleConfig['class'] }}">{{ $roleConfig['label'] }}</span>
@endsection

@section('content')
    <section class="stats-grid">
        <article class="stats-card income">
            <div class="stats-card-head">
                <span class="stats-label">Total Income</span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7 17L17 7M10 7h7v7" stroke="#aab3bc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stats-value"><strong>{{ \App\Support\Currency::inr($summary['total_income']) }}</strong></div>
        </article>

        <article class="stats-card expense">
            <div class="stats-card-head">
                <span class="stats-label">Total Expense</span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7 7l10 10M14 17h3v-3" stroke="#aab3bc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stats-value"><strong>{{ \App\Support\Currency::inr($summary['total_expense']) }}</strong></div>
        </article>

        <article class="stats-card balance">
            <div class="stats-card-head">
                <span class="stats-label">Balance</span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3v18M16.5 7.5C16.5 6.1 14.48 5 12 5s-4.5 1.1-4.5 3.5S9.52 12 12 12s4.5 1.1 4.5 3.5S14.48 19 12 19s-4.5-1.1-4.5-3.5" stroke="#aab3bc" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="stats-value"><strong>{{ \App\Support\Currency::inr($summary['balance']) }}</strong></div>
        </article>

        <article class="stats-card records">
            <div class="stats-card-head">
                <span class="stats-label">Total Records</span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7 4h7l5 5v9a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="#aab3bc" stroke-width="1.8"/>
                    <path d="M14 4v5h5" stroke="#aab3bc" stroke-width="1.8"/>
                </svg>
            </div>
            <div class="stats-value"><strong>{{ $summary['record_count'] }}</strong></div>
        </article>
    </section>

    <section class="dashboard-main">
        <div class="chart-grid">
            <article class="chart-card">
                <div class="card-head">
                    <h2 class="card-title">Income by Category</h2>
                </div>

                @if ($incomeGroups->isEmpty())
                    <div class="empty-card">No income records available for this time period.</div>
                @else
                    <div class="pie-layout">
                        <div class="pie-chart" style="{{ $pieStyle }}"></div>

                        @foreach ($incomeGroups as $index => $group)
                            @php($percent = round(((float) $group['total'] / $incomeTotal) * 100))
                            <span class="pie-label {{ $positions[$index] }}">{{ ucfirst($group['category']) }} {{ $percent }}%</span>
                        @endforeach
                    </div>
                @endif
            </article>

            <article class="chart-card">
                <div class="card-head">
                    <h2 class="card-title">Expense by Category</h2>
                </div>

                @if ($expenseGroups->isEmpty())
                    <div class="empty-card">No expense records available for this time period.</div>
                @else
                    <div class="bar-chart-shell">
                        <div class="bar-axis">
                            @foreach ($expenseAxisSteps as $step)
                                <span>{{ \App\Support\Currency::indianNumber($step) }}</span>
                            @endforeach
                            <span>0</span>
                        </div>

                        <div class="bar-chart">
                            @foreach (range(0, 4) as $row)
                                <div class="bar-line" style="bottom: {{ $row * 25 }}%;"></div>
                            @endforeach

                            <div class="bar-grid">
                                @foreach ($expenseGroups as $group)
                                    @php($height = max(6, round(((float) $group['total'] / $expenseAxisMax) * 100)))
                                    <div class="bar-column" style="--bar-height: {{ $height }}%;" data-tooltip="{{ $group['category'] }} · {{ \App\Support\Currency::inr($group['total']) }}">
                                        <div class="bar"></div>
                                        <div class="bar-label">{{ $group['category'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </article>
        </div>

        <div class="bottom-grid">
            <article class="latest-card">
                <div class="card-head">
                    <h2 class="card-title">Latest Records</h2>
                    <a class="view-link" href="{{ route('app.financial-records.index') }}">View All</a>
                </div>

                @if (collect($summary['latest_records'])->isEmpty())
                    <div class="empty-card">No records found for this time period.</div>
                @else
                    <div class="latest-list">
                        @foreach ($summary['latest_records'] as $record)
                            <div class="latest-item">
                                <div>
                                    <div class="latest-title">
                                        <strong>{{ $record->title }}</strong>
                                        <span class="type-pill {{ $record->type }}">{{ $record->type }}</span>
                                    </div>
                                    <div class="record-meta">{{ strtolower($record->category) }} • {{ $record->record_date->format('d/m/Y') }}</div>
                                </div>

                                <div class="amount-stack">
                                    <span class="money {{ $record->type }}">{{ \App\Support\Currency::inr($record->amount) }}</span>
                                    <a class="view-link" href="{{ route('app.financial-records.show', $record) }}">View</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>

            <aside class="capabilities-card">
                <div class="card-head">
                    <h2 class="card-title">Your Capabilities</h2>
                </div>

                <div>
                    <div class="section-copy" style="margin-bottom: 8px;">Current Role</div>
                    <span class="role-badge {{ $roleConfig['class'] }}">{{ ucfirst($user->role) }}</span>
                </div>

                <div>
                    <div class="section-copy" style="margin-bottom: 8px;">Permissions</div>
                    <ul class="capability-list">
                        @foreach ($roleConfig['permissions'] as $permission)
                            <li>{{ $permission }}</li>
                        @endforeach
                    </ul>
                </div>

                @if ($canManageRecords)
                    <div>
                        <div class="section-copy" style="margin-bottom: 10px;">Quick Actions</div>
                        <div class="stack-actions">
                            <a class="btn btn-primary btn-block" href="{{ route('app.financial-records.create') }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <span>New Record</span>
                            </a>
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>
@endsection
