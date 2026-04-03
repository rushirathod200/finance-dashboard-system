<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\InteractsWithFinancialFilters;
use App\Models\FinancialRecord;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use InteractsWithFinancialFilters;

    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', FinancialRecord::class);

        $selectedPeriod = $request->validate([
            'period' => ['nullable', Rule::in(['last_7_days', 'last_30_days', 'last_90_days', 'all_time'])],
        ])['period'] ?? 'last_30_days';

        $filters = $this->validateFinancialFilters($request);

        if (! ($filters['record_date'] ?? null) && ! ($filters['date_from'] ?? null) && ! ($filters['date_to'] ?? null)) {
            $dateRange = match ($selectedPeriod) {
                'last_7_days' => [now()->subDays(6)->toDateString(), now()->toDateString()],
                'last_90_days' => [now()->subDays(89)->toDateString(), now()->toDateString()],
                'all_time' => [null, null],
                default => [now()->subDays(29)->toDateString(), now()->toDateString()],
            };

            if ($dateRange[0] && $dateRange[1]) {
                $filters['date_from'] = $dateRange[0];
                $filters['date_to'] = $dateRange[1];
            }
        }

        $summary = $this->dashboardService->summary($filters);

        return view('dashboard.index', [
            'filters' => $filters,
            'summary' => $summary,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }
}
