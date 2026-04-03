<?php

namespace Tests\Feature;

use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FinancialRecordApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_analyst_can_create_and_filter_financial_records(): void
    {
        $analyst = User::factory()->create([
            'role' => User::ROLE_ANALYST,
        ]);

        Sanctum::actingAs($analyst);

        $createResponse = $this->postJson('/api/financial-records', [
            'title' => 'March Salary',
            'description' => 'Monthly salary credit',
            'type' => FinancialRecord::TYPE_INCOME,
            'category' => 'salary',
            'amount' => 4500.75,
            'record_date' => '2026-03-15',
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.type', FinancialRecord::TYPE_INCOME)
            ->assertJsonPath('data.creator.email', $analyst->email);

        FinancialRecord::factory()->create([
            'title' => 'Office Rent',
            'type' => FinancialRecord::TYPE_EXPENSE,
            'category' => 'rent',
            'amount' => 1500,
            'record_date' => '2026-03-12',
            'created_by' => $analyst->id,
        ]);

        $this->getJson('/api/financial-records?type=income&search=Salary')
            ->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.items.0.category', 'Salary');
    }

    public function test_financial_record_validation_uses_standard_error_format(): void
    {
        Sanctum::actingAs(User::factory()->create([
            'role' => User::ROLE_ANALYST,
        ]));

        $this->postJson('/api/financial-records', [
            'title' => '',
            'type' => 'invalid',
            'amount' => -10,
        ])->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed')
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['title', 'type', 'category', 'amount', 'record_date'],
            ]);
    }
}
