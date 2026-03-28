<?php

namespace Database\Factories;

use App\Models\{Project, Ticket, User};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'  => Project::factory(),
            'user_id'     => User::factory(),
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
        ];
    }
}
