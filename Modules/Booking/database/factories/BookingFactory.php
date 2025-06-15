<?php

namespace Modules\Booking\Database\Factories;

use Modules\Booking\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
            return [
            'user_id' => \App\Models\User::factory(), // or a valid user ID
            'team_id' => 1, // adjust as needed or use a Team factory if it exists
            'booking_date' => $this->faker->date(),
            'start_time' => $this->faker->time('H:i:s'),
            'end_time' => $this->faker->time('H:i:s'),
            'status' => 'pending', // or use faker->randomElement(['pending', 'confirmed', 'canceled'])
            'notes' => $this->faker->sentence(),
        ];
    }
}
