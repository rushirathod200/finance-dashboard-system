<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_income' => $this['total_income'],
            'total_expense' => $this['total_expense'],
            'balance' => $this['balance'],
            'record_count' => $this['record_count'],
            'income_by_category' => $this['income_by_category'],
            'expense_by_category' => $this['expense_by_category'],
            'latest_records' => FinancialRecordResource::collection($this['latest_records']),
        ];
    }
}
