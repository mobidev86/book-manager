<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name,
            'isbn' => $this->faker->unique()->isbn13,
            'published_at' => $this->faker->date,
            'copies' => $this->faker->randomDigitNotNull,
        ];
    }
}
