<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $amount = $this->faker->randomFloat(2, 10, 200);
        $discountPercentage = $this->faker->optional()->numberBetween(0, 30);
        $discountAmount = $this->faker->optional()->randomFloat(2, 0, 50);
        
        $subTotal = ($quantity * $amount);
        if ($discountPercentage) {
            $subTotal = $subTotal * (1 - $discountPercentage / 100);
        } elseif ($discountAmount) {
            $subTotal = max(0, $subTotal - $discountAmount);
        }

        return [
            'id' => (string) Str::uuid(),
            'invoice_id' => Invoice::factory(),
            'item_id' => Service::factory(),
            'item_description' => $this->faker->sentence(3),
            'quantity' => $quantity,
            'amount' => $amount,
            'discount_percentage' => $discountPercentage ?? 0,
            'discount_amount' => $discountAmount ?? 0,
            'sub_total' => round($subTotal, 2),
        ];
    }

    public function withPercentageDiscount(int $percentage): static
    {
        return $this->state(function (array $attributes) use ($percentage) {
            $quantity = $attributes['quantity'] ?? $this->faker->numberBetween(1, 5);
            $amount = $attributes['amount'] ?? $this->faker->randomFloat(2, 10, 200);
            $subTotal = ($quantity * $amount) * (1 - $percentage / 100);

            return [
                'discount_percentage' => $percentage,
                'discount_amount' => 0,
                'sub_total' => round($subTotal, 2),
            ];
        });
    }

    public function withAmountDiscount(float $discountAmount): static
    {
        return $this->state(function (array $attributes) use ($discountAmount) {
            $quantity = $attributes['quantity'] ?? $this->faker->numberBetween(1, 5);
            $amount = $attributes['amount'] ?? $this->faker->randomFloat(2, 10, 200);
            $subTotal = max(0, ($quantity * $amount) - $discountAmount);

            return [
                'discount_percentage' => 0,
                'discount_amount' => $discountAmount,
                'sub_total' => round($subTotal, 2),
            ];
        });
    }
} 