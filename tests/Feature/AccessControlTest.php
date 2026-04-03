<?php

namespace Tests\Feature;

use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewer_cannot_create_financial_records(): void
    {
        Sanctum::actingAs(User::factory()->create([
            'role' => User::ROLE_VIEWER,
        ]));

        $this->postJson('/api/financial-records', [
            'title' => 'Salary',
            'type' => FinancialRecord::TYPE_INCOME,
            'category' => 'salary',
            'amount' => 5000,
            'record_date' => '2026-03-01',
        ])->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_analyst_cannot_manage_users(): void
    {
        Sanctum::actingAs(User::factory()->create([
            'role' => User::ROLE_ANALYST,
        ]));

        $this->getJson('/api/users')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_admin_can_manage_users(): void
    {
        Sanctum::actingAs(User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]));

        $this->postJson('/api/users', [
            'name' => 'Finance Viewer',
            'email' => 'finance-viewer@example.com',
            'password' => 'password123',
            'role' => User::ROLE_VIEWER,
            'is_active' => true,
        ])->assertCreated()
            ->assertJsonPath('data.role', User::ROLE_VIEWER);
    }

    public function test_admin_cannot_change_own_role_or_deactivate_self_via_api(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        Sanctum::actingAs($admin);

        $this->putJson('/api/users/'.$admin->id, [
            'role' => User::ROLE_VIEWER,
        ])->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed');

        $this->deleteJson('/api/users/'.$admin->id)
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed');
    }
}
