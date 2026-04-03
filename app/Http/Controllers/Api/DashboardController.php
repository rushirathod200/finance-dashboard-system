<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialRecordFilterRequest;
use App\Http\Resources\DashboardSummaryResource;
use App\Models\FinancialRecord;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    public function summary(FinancialRecordFilterRequest $request): JsonResponse
    {
        $this->authorize('viewAny', FinancialRecord::class);

        $summary = $this->dashboardService->summary($request->validated());

        return $this->successResponse(new DashboardSummaryResource($summary), 'Dashboard summary retrieved successfully.');
    }
}
