@extends('layouts.app')

@php($canManageRecords = auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ANALYST))

@section('page_title', $record->title)
@section('page_subtitle', 'Detailed view of the selected financial record')

@section('page_tools')
    <a class="btn btn-secondary" href="{{ route('app.financial-records.index') }}">Back to Records</a>

    @if ($canManageRecords)
        <a class="btn btn-primary" href="{{ route('app.financial-records.edit', $record) }}">Edit Record</a>
    @endif
@endsection

@section('content')
    <section class="detail-layout">
        <article class="form-card">
            <div class="card-head">
                <div>
                    <h2 class="card-title">{{ $record->title }}</h2>
                    <p class="card-copy">{{ $record->description ?: 'No additional description was provided for this record.' }}</p>
                </div>

                <span class="type-pill {{ $record->type }}">{{ $record->type }}</span>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Category</span>
                    <span class="detail-value">{{ $record->category }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Amount</span>
                    <span class="detail-value">{{ \App\Support\Currency::inr($record->amount) }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Record Date</span>
                    <span class="detail-value">{{ $record->record_date->format('d/m/Y') }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Created At</span>
                    <span class="detail-value">{{ $record->created_at?->format('d/m/Y · h:i A') }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Created By</span>
                    <span class="detail-value">{{ $record->creator?->name ?? 'Unknown' }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Updated By</span>
                    <span class="detail-value">{{ $record->updater?->name ?? 'Not updated yet' }}</span>
                </div>
            </div>
        </article>

        <aside class="info-card">
            <div class="card-head">
                <h2 class="card-title">Record Actions</h2>
            </div>

            <ul class="info-list">
                <li>Use edit to correct the transaction while keeping the audit trail intact.</li>
                <li>This entry contributes directly to the dashboard summary.</li>
                <li>Delete should be used carefully because it removes the record from reports.</li>
            </ul>

            @if ($canManageRecords)
                <form method="POST" action="{{ route('app.financial-records.destroy', $record) }}" onsubmit="return confirm('Delete this financial record?');" style="margin-top: 18px;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Delete Record</button>
                </form>
            @endif
        </aside>
    </section>
@endsection
