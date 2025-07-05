<?php

namespace Database\Factories;

use App\Models\HeadingAccount;
use App\Models\MainAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class HeadingAccountFactory extends Factory
{
    protected $model = HeadingAccount::class;

    public function definition(): array
    {
        return [
            'heading_code' => $this->faker->unique()->numberBetween(100, 10000),
            'heading_account' => $this->faker->words(2, true),
            'main_code' => MainAccount::inRandomOrder()->first()->main_code ?? 1,
        ];
    }
}
