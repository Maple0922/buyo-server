<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $baseDatetime = Carbon::today();
        $randomAddHours = fake()->numberBetween(8, 20);
        $randomAddDays = fake()->numberBetween(0, 20);
        return [
            'name' => fake()->word(),
            'start' => $baseDatetime->clone()->addDays($randomAddDays)->addHours($randomAddHours),
            'end' => $baseDatetime->clone()->addDays($randomAddDays)->addHours($randomAddHours + 2),
            'passcode' => fake()->randomNumber(4),
        ];
    }
}
