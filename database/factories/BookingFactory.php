<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 month');
        $endTime = clone $startTime;
        $endTime->modify('+1 hour');

        return [
            'customer_id' => Customer::factory(),
            'employee_id' => Employee::factory(),
            'service_id' => Service::factory(),
            'booking_date' => $startTime->format('Y-m-d'),
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
            ];
        });
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
} 