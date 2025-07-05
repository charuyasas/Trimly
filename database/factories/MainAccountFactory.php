<?php

namespace Database\Factories;

use App\Models\MainAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class MainAccountFactory extends Factory
{
    protected $model = MainAccount::class;

    public function definition(): array
    {
        return [
            'main_code' => $this->faker->unique()->numberBetween(1000, 9999),
            'main_account' => $this->faker->word, 
        ];
    }
}
