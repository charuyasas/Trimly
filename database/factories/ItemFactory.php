<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\SubCategory;

class ItemFactory extends Factory
{
    protected $model = \App\Models\Item::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'code' => strtoupper('ITM' . $this->faker->unique()->numerify('###')),
            'description' => $this->faker->sentence(3),
            'rack_location' => $this->faker->optional()->regexify('[A-Z]{1}[0-9]{2}'),
            'supplier_id' => Supplier::inRandomOrder()->first()->id ?? Supplier::factory(),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'sub_category_id' => SubCategory::inRandomOrder()->first()->id ?? null,
            'measure_unit' => $this->faker->randomElement(['Kg', 'g', 'unit', 'l', 'ml']),
            'is_active' => $this->faker->boolean(90),
            'list_price' => $this->faker->randomFloat(2, 10, 500),
            'retail_price' => $this->faker->randomFloat(2, 10, 500),
            'wholesale_price' => $this->faker->randomFloat(2, 5, 450),
        ];
    }
}
