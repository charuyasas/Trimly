<?php

namespace Database\Factories;

use App\Models\MainAccount;
use App\Models\HeadingAccount;
use App\Models\TitleAccount;
use App\Models\PostingAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostingAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'posting_account' => $this->faker->unique()->words(3, true),
            'main_code' => null, // will be set in afterMaking/afterCreating if not provided
            'heading_code' => null, // will be set in afterMaking/afterCreating if not provided
            'title_code' => null, // will be set in afterMaking/afterCreating if not provided
            'ledger_code' => $this->faker->unique()->numerify('####-####-####-####'),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (PostingAccount $postingAccount) {
            if (!$postingAccount->main_code) {
                $mainAccount = MainAccount::factory()->create();
                $postingAccount->main_code = $mainAccount->main_code;
            }
            if (!$postingAccount->heading_code) {
                $headingAccount = HeadingAccount::factory()->create([
                    'main_code' => $postingAccount->main_code
                ]);
                $postingAccount->heading_code = $headingAccount->heading_code;
            }
            if (!$postingAccount->title_code) {
                $titleAccount = TitleAccount::factory()->create([
                    'main_code' => $postingAccount->main_code,
                    'heading_code' => $postingAccount->heading_code
                ]);
                $postingAccount->title_code = $titleAccount->title_code;
            }
        })->afterCreating(function (PostingAccount $postingAccount) {
            $changed = false;
            if (!$postingAccount->main_code) {
                $mainAccount = MainAccount::factory()->create();
                $postingAccount->main_code = $mainAccount->main_code;
                $changed = true;
            }
            if (!$postingAccount->heading_code) {
                $headingAccount = HeadingAccount::factory()->create([
                    'main_code' => $postingAccount->main_code
                ]);
                $postingAccount->heading_code = $headingAccount->heading_code;
                $changed = true;
            }
            if (!$postingAccount->title_code) {
                $titleAccount = TitleAccount::factory()->create([
                    'main_code' => $postingAccount->main_code,
                    'heading_code' => $postingAccount->heading_code
                ]);
                $postingAccount->title_code = $titleAccount->title_code;
                $changed = true;
            }
            if ($changed) {
                $postingAccount->save();
            }
        });
    }
} 