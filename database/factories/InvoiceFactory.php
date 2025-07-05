<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'invoice_no' => $this->faker->unique()->numerify('INV####'),
            'employee_no' => Employee::factory(),
            'customer_no' => Customer::factory(),
            'grand_total' => $this->faker->randomFloat(2, 50, 1000),
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'status' => $this->faker->randomElement([0, 1, 2]), // pending, complete, finish
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }

    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
        ]);
    }
} 