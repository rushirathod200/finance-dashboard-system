<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialRecordFilterRequest;
use App\Http\Requests\StoreFinancialRecordRequest;
use App\Http\Requests\UpdateFinancialRecordRequest;
use App\Http\Resources\FinancialRecordResource;
use App\Models\FinancialRecord;
use App\Services\FinancialRecordService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FinancialRecordController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected FinancialRecordService $financialRecordService
    ) {
    }

    public function index(FinancialRecordFilterRequest $request): JsonResponse
    {
        $this->authorize('viewAny', FinancialRecord::class);

        $records = $this->financialRecordService->paginate($request->validated());

        return $this->successResponse([
            'items' => FinancialRecordResource::collection($records->getCollection()),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
            'filters' => $request->validated(),
        ], 'Financial records retrieved successfully.');
    }

    public function store(StoreFinancialRecordRequest $request): JsonResponse
    {
        $this->authorize('create', FinancialRecord::class);

        $record = FinancialRecord::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ])->load(['creator', 'updater']);

        return $this->successResponse(new FinancialRecordResource($record), 'Financial record created successfully.', Response::HTTP_CREATED);
    }

    public function show(FinancialRecord $financialRecord): JsonResponse
    {
        $this->authorize('view', $financialRecord);

        $financialRecord->load(['creator', 'updater']);

        return $this->successResponse(new FinancialRecordResource($financialRecord), 'Financial record retrieved successfully.');
    }

    public function update(UpdateFinancialRecordRequest $request, FinancialRecord $financialRecord): JsonResponse
    {
        $this->authorize('update', $financialRecord);

        $financialRecord->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return $this->successResponse(
            new FinancialRecordResource($financialRecord->fresh(['creator', 'updater'])),
            'Financial record updated successfully.'
        );
    }

    public function destroy(FinancialRecord $financialRecord): JsonResponse
    {
        $this->authorize('delete', $financialRecord);

        $financialRecord->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
