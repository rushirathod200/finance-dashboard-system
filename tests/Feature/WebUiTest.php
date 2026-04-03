<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_root(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Finance Control')
            ->assertSee('Sign In')
            ->assertSee('Demo Accounts');
    }

    public function test_guest_is_redirected_to_login_from_protected_web_route(): void
    {
        $this->get(route('app.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_active_user_can_sign_in_and_view_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'analyst-ui@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_ANALYST,
        ]);

        $this->post(route('app.login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('app.dashboard'));

        $this->actingAs($user)
            ->get(route('app.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Your Capabilities');
    }

    public function test_analyst_can_open_create_financial_record_screen(): void
    {
        $analyst = User::factory()->create([
            'role' => User::ROLE_ANALYST,
        ]);

        $this->actingAs($analyst)
            ->get(route('app.financial-records.create'))
            ->assertOk()
            ->assertSee('Create Financial Record')
            ->assertSee('New Record')
            ->assertSee('Select category')
            ->assertSee('Salary')
            ->assertSee('Freelance')
            ->assertSee('Investment');
    }

    public function test_viewer_cannot_open_user_management_screen(): void
    {
        $viewer = User::factory()->create([
            'role' => User::ROLE_VIEWER,
        ]);

        $this->actingAs($viewer)
            ->get(route('app.users.index'))
            ->assertForbidden()
            ->assertSee('Permission denied')
            ->assertSee('Return to Dashboard');
    }

    public function test_admin_can_open_user_management_screen(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->get(route('app.users.index'))
            ->assertOk()
            ->assertSee('User Management')
            ->assertSee('Create User');
    }

    public function test_web_validation_errors_redirect_back_with_session_errors(): void
    {
        $analyst = User::factory()->create([
            'role' => User::ROLE_ANALYST,
        ]);

        $this->actingAs($analyst)
            ->from(route('app.financial-records.create'))
            ->post(route('app.financial-records.store'), [
                'title' => '',
                'type' => 'invalid',
                'amount' => -10,
            ])
            ->assertRedirect(route('app.financial-records.create'))
            ->assertSessionHasErrors(['title', 'type', 'category', 'amount', 'record_date']);
    }
}
