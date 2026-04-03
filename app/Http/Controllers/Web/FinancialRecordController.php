<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\InteractsWithFinancialFilters;
use App\Http\Requests\StoreFinancialRecordRequest;
use App\Http\Requests\UpdateFinancialRecordRequest;
use App\Models\FinancialRecord;
use App\Services\FinancialRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialRecordController extends Controller
{
    use InteractsWithFinancialFilters;

    public function __construct(
        protected FinancialRecordService $financialRecordService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', FinancialRecord::class);

        $filters = $this->validateFinancialFilters($request);
        $records = $this->financialRecordService->paginate($filters);

        return view('financial-records.index', [
            'filters' => $filters,
            'records' => $records,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', FinancialRecord::class);

        return view('financial-records.form', [
            'record' => new FinancialRecord(),
            'categoryOptions' => FinancialRecord::categoryOptions(),
            'isEdit' => false,
        ]);
    }

    public function store(StoreFinancialRecordRequest $request): RedirectResponse
    {
        $this->authorize('create', FinancialRecord::class);

        $record = FinancialRecord::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('app.financial-records.show', $record)
            ->with('status', 'Financial record created successfully.');
    }

    public function show(FinancialRecord $financialRecord): View
    {
        $this->authorize('view', $financialRecord);

        $financialRecord->load(['creator', 'updater']);

        return view('financial-records.show', [
            'record' => $financialRecord,
        ]);
    }

    public function edit(FinancialRecord $financialRecord): View
    {
        $this->authorize('update', $financialRecord);

        return view('financial-records.form', [
            'record' => $financialRecord,
            'categoryOptions' => FinancialRecord::categoryOptions(),
            'isEdit' => true,
        ]);
    }

    public function update(UpdateFinancialRecordRequest $request, FinancialRecord $financialRecord): RedirectResponse
    {
        $this->authorize('update', $financialRecord);

        $financialRecord->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('app.financial-records.show', $financialRecord)
            ->with('status', 'Financial record updated successfully.');
    }

    public function destroy(FinancialRecord $financialRecord): RedirectResponse
    {
        $this->authorize('delete', $financialRecord);

        $financialRecord->delete();

        return redirect()
            ->route('app.financial-records.index')
            ->with('status', 'Financial record deleted successfully.');
    }
}
