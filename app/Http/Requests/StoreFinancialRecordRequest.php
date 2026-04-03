<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;
use App\Models\FinancialRecord;
use Illuminate\Validation\Rule;

class StoreFinancialRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'category' => FinancialRecord::normalizeCategory(
                $this->input('type'),
                $this->input('category')
            ),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(FinancialRecord::types())],
            'category' => ['required', Rule::in(FinancialRecord::categoriesForType($this->input('type')))],
            'amount' => ['required', 'numeric', 'gt:0'],
            'record_date' => ['required', 'date'],
        ];
    }
}
