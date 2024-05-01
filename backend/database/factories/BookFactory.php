<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code_book' => $this->faker->unique()->numerify('##########'),
            'isbn' => $this->faker->isbn13(),
            'title_book' => $this->faker->sentence(3),
            'publisher' => $this->faker->company(),
            'code_category' => $this->faker->unique()->numerify('##'),
            'code_author' => $this->faker->unique()->numerify('##'),
            'code_rack' => $this->faker->unique()->numerify('##'),
            'stok' => $this->faker->numberBetween(0, 100),
            'loan_amount' => $this->faker->numberBetween(0, 100),
        ];
    }
}
