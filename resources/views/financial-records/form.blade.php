@extends('layouts.app')

@php
    $selectedType = old('type', $record->type ?: \App\Models\FinancialRecord::TYPE_INCOME);
    $selectedCategory = old('category', $record->category);
    $currentCategoryOptions = $categoryOptions[$selectedType] ?? [];
@endphp

@section('page_title', $isEdit ? 'Edit Financial Record' : 'Create Financial Record')
@section('page_subtitle', $isEdit ? 'Update the selected transaction and keep dashboard totals accurate' : 'Add a new transaction to the financial ledger')

@section('page_tools')
    <a class="btn btn-secondary" href="{{ $isEdit ? route('app.financial-records.show', $record) : route('app.financial-records.index') }}">Back</a>
@endsection

@section('content')
    <section class="form-layout">
        <form class="form-card" method="POST" action="{{ $isEdit ? route('app.financial-records.update', $record) : route('app.financial-records.store') }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="card-head">
                <h2 class="card-title">{{ $isEdit ? 'Update Record' : 'New Record' }}</h2>
            </div>

            <div class="form-grid">
                <div class="field span-2">
                    <label for="title">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $record->title) }}" required>
                </div>

                <div class="field span-2">
                    <label for="description">Description</label>
                    <textarea id="description" name="description">{{ old('description', $record->description) }}</textarea>
                </div>

                <div class="field">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="income" @selected($selectedType === 'income')>Income</option>
                        <option value="expense" @selected($selectedType === 'expense')>Expense</option>
                    </select>
                </div>

                <div class="field">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select category</option>
                        @foreach ($currentCategoryOptions as $category)
                            <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="amount">Amount</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount', $record->amount) }}" required>
                </div>

                <div class="field">
                    <label for="record_date">Record Date</label>
                    <input id="record_date" name="record_date" type="date" value="{{ old('record_date', optional($record->record_date)->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="filter-actions">
                <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Save Changes' : 'Create Record' }}</button>
                <a class="btn btn-secondary" href="{{ route('app.financial-records.index') }}">Cancel</a>
            </div>
        </form>

        <aside class="info-card">
            <div class="card-head">
                <h2 class="card-title">Form Rules</h2>
            </div>

            <ul class="info-list">
                <li>Amounts are always positive values.</li>
                <li>The selected type decides whether the amount is income or expense.</li>
                <li>Every saved record updates dashboard totals immediately.</li>
                <li>Creator and updater information is tracked automatically.</li>
            </ul>

            @if ($isEdit)
                <div class="detail-item" style="margin-top: 18px;">
                    <span class="detail-label">Created By</span>
                    <span class="detail-value">{{ $record->creator?->name ?? 'Unknown' }}</span>
                </div>
            @endif
        </aside>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeSelect = document.getElementById('type');
            const categorySelect = document.getElementById('category');
            const categoryOptions = @json($categoryOptions);
            const initialCategory = @json($selectedCategory);

            if (!typeSelect || !categorySelect) {
                return;
            }

            const renderCategories = (type, preferredValue = '') => {
                const categories = categoryOptions[type] || [];
                const currentValue = preferredValue || categorySelect.value;

                categorySelect.innerHTML = '';

                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = 'Select category';
                categorySelect.appendChild(placeholderOption);

                categories.forEach((category) => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;

                    if (category === currentValue) {
                        option.selected = true;
                    }

                    categorySelect.appendChild(option);
                });

                if (!categories.includes(currentValue)) {
                    categorySelect.value = '';
                }
            };

            renderCategories(typeSelect.value, initialCategory);

            typeSelect.addEventListener('change', () => {
                renderCategories(typeSelect.value, '');
            });
        });
    </script>
@endsection
