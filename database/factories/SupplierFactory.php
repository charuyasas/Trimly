<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'id'           => Str::uuid(),
            'supplier_code'=> 'SUP-' . strtoupper(Str::random(6)),
            'name'         => $this->faker->company,
            'contact_no'   => $this->faker->numerify('07########'),
            'email'        => $this->faker->unique()->safeEmail,
            'address'      => $this->faker->address,
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
