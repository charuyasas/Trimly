<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BankFactory extends Factory
{

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'bank_code' => strtoupper($this->faker->unique()->bothify('???###')),
            'bank_name' => $this->faker->optional()->sentence(),
        ];
    }
}
