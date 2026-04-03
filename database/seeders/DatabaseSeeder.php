<?php

namespace Database\Seeders;

use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $priya = User::factory()->create([
            'name' => 'Priya Sharma',
            'email' => 'priya.viewer@financecontrol.in',
            'password' => Hash::make('password'),
            'role' => User::ROLE_VIEWER,
        ]);

        $arjun = User::factory()->create([
            'name' => 'Arjun Mehta',
            'email' => 'arjun.analyst@financecontrol.in',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ANALYST,
        ]);

        $kavya = User::factory()->create([
            'name' => 'Kavya Reddy',
            'email' => 'kavya.admin@financecontrol.in',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Rahul Verma',
            'email' => 'rahul.verma@financecontrol.in',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ANALYST,
        ]);

        User::factory()->create([
            'name' => 'Sneha Patil',
            'email' => 'sneha.patil@financecontrol.in',
            'password' => Hash::make('password'),
            'role' => User::ROLE_VIEWER,
            'is_active' => false,
        ]);

        $records = [
            [
                'title' => 'Mutual Fund Dividend',
                'description' => 'Quarterly dividend payout from mutual fund investments',
                'type' => FinancialRecord::TYPE_INCOME,
                'category' => 'Investment',
                'amount' => 4500.00,
                'record_date' => '2026-03-30',
                'created_by' => $kavya->id,
            ],
            [
                'title' => 'Swiggy & Movie Night',
                'description' => 'Weekend entertainment and food delivery expense',
                'type' => FinancialRecord::TYPE_EXPENSE,
                'category' => 'Entertainment',
                'amount' => 1250.00,
                'record_date' => '2026-03-29',
                'created_by' => $arjun->id,
            ],
            [
                'title' => 'BigBasket Groceries',
                'description' => 'Weekly household groceries ordered through BigBasket',
                'type' => FinancialRecord::TYPE_EXPENSE,
                'category' => 'Groceries',
                'amount' => 4850.00,
                'record_date' => '2026-03-28',
                'created_by' => $arjun->id,
            ],
            [
                'title' => 'Petrol Refill',
                'description' => 'Monthly fuel spend for the family car',
                'type' => FinancialRecord::TYPE_EXPENSE,
                'category' => 'Transport',
                'amount' => 3600.00,
                'record_date' => '2026-03-26',
                'created_by' => $kavya->id,
            ],
            [
                'title' => 'Monthly Salary Credit',
                'description' => 'March 2026 salary credited by employer',
                'type' => FinancialRecord::TYPE_INCOME,
                'category' => 'Salary',
                'amount' => 85000.00,
                'record_date' => '2026-03-25',
                'created_by' => $kavya->id,
            ],
            [
                'title' => 'Apollo Clinic Visit',
                'description' => 'General consultation and prescribed tests',
                'type' => FinancialRecord::TYPE_EXPENSE,
                'category' => 'Healthcare',
                'amount' => 1750.00,
                'record_date' => '2026-03-22',
                'created_by' => $kavya->id,
            ],
            [
                'title' => 'Freelance Project - Shopify Store',
                'description' => 'Payment received for a freelance Shopify storefront build',
                'type' => FinancialRecord::TYPE_INCOME,
                'category' => 'Freelance',
                'amount' => 25000.00,
                'record_date' => '2026-03-20',
                'created_by' => $arjun->id,
            ],
            [
                'title' => 'Freelance Project - Brand Logo',
                'description' => 'Logo and identity work for a startup client',
                'type' => FinancialRecord::TYPE_INCOME,
                'category' => 'Freelance',
                'amount' => 12000.00,
                'record_date' => '2026-03-18',
                'created_by' => $arjun->id,
            ],
            [
                'title' => 'Electricity Bill',
                'description' => 'Monthly electricity bill payment for March',
                'type' => FinancialRecord::TYPE_EXPENSE,
                'category' => 'Utilities',
                'amount' => 2450.00,
                'record_date' => '2026-03-15',
                'created_by' => $kavya->id,
            ],
            [
                'title' => 'Metro Card Recharge',
                'description' => 'Monthly metro travel card recharge',
                'type' => FinancialRecord::TYPE_EXPENSE,
                'category' => 'Transport',
                'amount' => 1200.00,
                'record_date' => '2026-03-12',
                'created_by' => $kavya->id,
            ],
            [
                'title' => 'Team Dinner',
                'description' => 'Dinner outing with the project team',
                'type' => FinancialRecord::TYPE_EXPENSE,
                'category' => 'Entertainment',
                'amount' => 1750.00,
                'record_date' => '2026-03-10',
                'created_by' => $arjun->id,
            ],
            [
                'title' => 'Flat Rent Payment',
                'description' => 'Monthly apartment rent transfer',
                'type' => FinancialRecord::TYPE_EXPENSE,
                'category' => 'Rent',
                'amount' => 22000.00,
                'record_date' => '2026-03-01',
                'created_by' => $kavya->id,
            ],
        ];

        foreach ($records as $record) {
            FinancialRecord::query()->create($record);
        }
    }
}
