<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(), // UUID primary key
            'employee_id' => strtoupper($this->faker->unique()->bothify('EMP###')), 
            'name' => $this->faker->name(),
            'address' => $this->faker->optional()->address(),
            'contact_no' => $this->faker->optional()->numerify('07#########'), 
        ];
    }
}
