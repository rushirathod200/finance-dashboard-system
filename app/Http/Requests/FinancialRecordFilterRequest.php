<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;
use App\Models\FinancialRecord;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FinancialRecordFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::in(FinancialRecord::types())],
            'category' => ['nullable', 'string', 'max:255'],
            'record_date' => ['nullable', 'date'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'min_amount' => ['nullable', 'numeric', 'gte:0'],
            'max_amount' => ['nullable', 'numeric', 'gte:0'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', Rule::in(['title', 'type', 'category', 'amount', 'record_date', 'created_at'])],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $dateFrom = $this->input('date_from');
            $dateTo = $this->input('date_to');
            $minAmount = $this->input('min_amount');
            $maxAmount = $this->input('max_amount');

            if ($dateFrom && $dateTo && $dateFrom > $dateTo) {
                $validator->errors()->add('date_from', 'The date_from field must be before or equal to date_to.');
            }

            if ($minAmount !== null && $maxAmount !== null && (float) $minAmount > (float) $maxAmount) {
                $validator->errors()->add('min_amount', 'The min_amount field must be less than or equal to max_amount.');
            }
        });
    }
}
