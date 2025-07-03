<?php

namespace Database\Factories;

use App\Models\TitleAccount;
use App\Models\MainAccount;
use App\Models\HeadingAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class TitleAccountFactory extends Factory
{
    protected $model = TitleAccount::class;

    public function definition(): array
    {
        $heading = HeadingAccount::inRandomOrder()->first();

        return [
            'title_account' => $this->faker->words(2, true),
            'main_code' => $heading->main_code ?? 1,
            'heading_code' => $heading->heading_code ?? 1,
        ];
    }
}
