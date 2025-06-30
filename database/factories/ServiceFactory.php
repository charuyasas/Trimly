<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(), 
            'code' => strtoupper($this->faker->unique()->bothify('SRV###')), 
            'description' => $this->faker->optional()->sentence(),
            'price' => $this->faker->randomFloat(2, 100, 5000), 
        ];
    }
}
