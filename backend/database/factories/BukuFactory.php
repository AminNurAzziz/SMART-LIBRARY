<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BukuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_buku' => $this->faker->unique()->numerify('##########'),
            'isbn' => $this->faker->isbn13(),
            'judul_buku' => $this->faker->sentence(3),
            'penerbit' => $this->faker->company(),
            'kode_kategori' => $this->faker->unique()->numerify('##'),
            'kode_penulis' => $this->faker->unique()->numerify('##'),
            'kode_rak' => $this->faker->unique()->numerify('##'),
            'stok' => $this->faker->numberBetween(0, 100),
            'jumlah_peminjam' => $this->faker->numberBetween(0, 100),
        ];
    }
}
