<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' => $this->faker->numberBetween(1, 10),
            'title' => $this->faker->word(),
            'content' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(),
            'is_pinned' => $this->faker->boolean()
        ];
    }
}
