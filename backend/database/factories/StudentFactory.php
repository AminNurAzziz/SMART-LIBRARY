<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nim' => $this->faker->unique()->numerify('##########'),
            'nama_mhs' => $this->faker->name(),
            'prodi_mhs' => $this->faker->word(),
            'kelas_mhs' => $this->faker->word(),
            'email_mhs' => $this->faker->unique()->safeEmail(),
            'status_mhs' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
