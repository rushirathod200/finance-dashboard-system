<?php

namespace App\Http\Controllers\Web\Concerns;

use App\Models\FinancialRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait InteractsWithFinancialFilters
{
    protected function validateFinancialFilters(Request $request): array
    {
        return $request->validate([
            'type' => ['nullable', Rule::in(FinancialRecord::types())],
            'category' => ['nullable', 'string', 'max:255'],
            'record_date' => ['nullable', 'date'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'min_amount' => ['nullable', 'numeric', 'gte:0'],
            'max_amount' => ['nullable', 'numeric', 'gte:min_amount'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', Rule::in(['title', 'type', 'category', 'amount', 'record_date', 'created_at'])],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
    }
}
