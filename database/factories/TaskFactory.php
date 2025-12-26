<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'done' => $this->faker->boolean(30),
            'priority' => $this->faker->randomElement(['low','normal','high']),
            'due_date' => $this->faker->boolean(60) ? $this->faker->dateTimeBetween('-2 days', '+10 days')->format('Y-m-d') : null,
        ];
    }

}
