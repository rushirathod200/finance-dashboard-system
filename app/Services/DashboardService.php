<?php

namespace App\Services;

use App\Models\FinancialRecord;
class DashboardService
{
    public function __construct(
        protected FinancialRecordService $financialRecordService
    ) {
    }

    public function summary(array $filters): array
    {
        $baseQuery = $this->financialRecordService->applyFilters(
            FinancialRecord::query(),
            $filters
        );

        $totalIncome = (clone $baseQuery)->where('type', FinancialRecord::TYPE_INCOME)->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', FinancialRecord::TYPE_EXPENSE)->sum('amount');

        $latestRecords = (clone $baseQuery)
            ->with(['creator', 'updater'])
            ->latest('record_date')
            ->limit(4)
            ->get();

        return [
            'total_income' => number_format((float) $totalIncome, 2, '.', ''),
            'total_expense' => number_format((float) $totalExpense, 2, '.', ''),
            'balance' => number_format((float) $totalIncome - (float) $totalExpense, 2, '.', ''),
            'record_count' => (clone $baseQuery)->count(),
            'income_by_category' => $this->categoryTotals(clone $baseQuery, FinancialRecord::TYPE_INCOME),
            'expense_by_category' => $this->categoryTotals(clone $baseQuery, FinancialRecord::TYPE_EXPENSE),
            'latest_records' => $latestRecords,
        ];
    }

    protected function categoryTotals($query, string $type): array
    {
        return $query
            ->selectRaw('category, SUM(amount) as total')
            ->where('type', $type)
            ->groupBy('category')
            ->orderBy('category')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'total' => number_format((float) $row->total, 2, '.', ''),
            ])
            ->values()
            ->all();
    }
}
