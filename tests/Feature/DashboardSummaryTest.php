<?php

namespace Tests\Feature;

use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_expected_totals_and_category_groups(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_VIEWER,
        ]);

        FinancialRecord::factory()->create([
            'title' => 'Salary',
            'type' => FinancialRecord::TYPE_INCOME,
            'category' => 'salary',
            'amount' => 5000,
            'record_date' => '2026-03-01',
            'created_by' => $user->id,
        ]);

        FinancialRecord::factory()->create([
            'title' => 'Freelance',
            'type' => FinancialRecord::TYPE_INCOME,
            'category' => 'freelance',
            'amount' => 1000,
            'record_date' => '2026-03-05',
            'created_by' => $user->id,
        ]);

        FinancialRecord::factory()->create([
            'title' => 'Rent',
            'type' => FinancialRecord::TYPE_EXPENSE,
            'category' => 'rent',
            'amount' => 1800,
            'record_date' => '2026-03-08',
            'created_by' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard/summary?date_from=2026-03-01&date_to=2026-03-31')
            ->assertOk()
            ->assertJsonPath('data.total_income', '6000.00')
            ->assertJsonPath('data.total_expense', '1800.00')
            ->assertJsonPath('data.balance', '4200.00')
            ->assertJsonPath('data.record_count', 3)
            ->assertJsonPath('data.income_by_category.0.category', 'freelance')
            ->assertJsonPath('data.expense_by_category.0.total', '1800.00');
    }
}
