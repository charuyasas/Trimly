<?php

namespace Database\Factories;

use App\Models\SidebarLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class SidebarLinkFactory extends Factory
{
    protected $model = SidebarLink::class;

    public function definition()
    {
        return [
            'display_name' => $this->faker->word,
            'url' => $this->faker->url,
            'icon_path' => null,
            'parent_id' => null,
            'permission_name' => $this->faker->unique()->word, // Always non-null
        ];
    }
} 