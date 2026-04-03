<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;
use App\Models\FinancialRecord;
use Illuminate\Validation\Rule;

class UpdateFinancialRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        /** @var FinancialRecord|null $financialRecord */
        $financialRecord = $this->route('financialRecord');
        $selectedType = $this->input('type', $financialRecord?->type);

        $this->merge([
            'category' => FinancialRecord::normalizeCategory(
                $selectedType,
                $this->input('category')
            ),
        ]);
    }

    public function rules(): array
    {
        /** @var FinancialRecord|null $financialRecord */
        $financialRecord = $this->route('financialRecord');
        $selectedType = $this->input('type', $financialRecord?->type);

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'required', Rule::in(FinancialRecord::types())],
            'category' => ['sometimes', 'required', Rule::in(FinancialRecord::categoriesForType($selectedType))],
            'amount' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'record_date' => ['sometimes', 'required', 'date'],
        ];
    }
}
