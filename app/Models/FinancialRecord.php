<?php

namespace App\Models;

use Database\Factories\FinancialRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'description',
    'type',
    'category',
    'amount',
    'record_date',
    'created_by',
    'updated_by',
])]
class FinancialRecord extends Model
{
    /** @use HasFactory<FinancialRecordFactory> */
    use HasFactory;

    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';
    public const CATEGORY_OPTIONS = [
        self::TYPE_INCOME => [
            'Salary',
            'Freelance',
            'Investment',
        ],
        self::TYPE_EXPENSE => [
            'Rent',
            'Groceries',
            'Utilities',
            'Transport',
            'Healthcare',
            'Entertainment',
        ],
    ];

    public static function types(): array
    {
        return [
            self::TYPE_INCOME,
            self::TYPE_EXPENSE,
        ];
    }

    public static function categoryOptions(): array
    {
        return self::CATEGORY_OPTIONS;
    }

    public static function categoriesForType(?string $type): array
    {
        if ($type === null) {
            return [];
        }

        return self::CATEGORY_OPTIONS[$type] ?? [];
    }

    public static function normalizeCategory(?string $type, null|string $category): ?string
    {
        if ($type === null || $category === null) {
            return $category;
        }

        foreach (self::categoriesForType($type) as $allowedCategory) {
            if (strcasecmp($allowedCategory, $category) === 0) {
                return $allowedCategory;
            }
        }

        return $category;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    #[Scope]
    protected function ofType($query, string $type): void
    {
        $query->where('type', $type);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'record_date' => 'date',
        ];
    }
}
