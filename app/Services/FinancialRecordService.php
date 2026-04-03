<?php

namespace App\Services;

use App\Models\FinancialRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class FinancialRecordService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = FinancialRecord::query()->with(['creator', 'updater']);

        $this->applyFilters($query, $filters);

        $sortBy = $filters['sort_by'] ?? 'record_date';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 10);

        return $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function applyFilters(Builder $query, array $filters): Builder
    {
        $query
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->when($filters['record_date'] ?? null, fn (Builder $query, string $recordDate) => $query->whereDate('record_date', $recordDate))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $dateFrom) => $query->whereDate('record_date', '>=', $dateFrom))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $dateTo) => $query->whereDate('record_date', '<=', $dateTo))
            ->when($filters['min_amount'] ?? null, fn (Builder $query, string|float $minAmount) => $query->where('amount', '>=', $minAmount))
            ->when($filters['max_amount'] ?? null, fn (Builder $query, string|float $maxAmount) => $query->where('amount', '<=', $maxAmount))
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $searchQuery) use ($search) {
                    $searchQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            });

        return $query;
    }
}
