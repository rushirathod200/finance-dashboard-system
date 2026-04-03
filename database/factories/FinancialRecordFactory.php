<?php

namespace Database\Factories;

use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<FinancialRecord>
 */
class FinancialRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'type' => Arr::random(FinancialRecord::types()),
            'category' => Arr::random(['salary', 'freelance', 'rent', 'groceries', 'utilities', 'transport']),
            'amount' => fake()->randomFloat(2, 100, 10000),
            'record_date' => fake()->date(),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
