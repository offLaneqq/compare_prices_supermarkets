<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Модель, з якою працює ця фабрика.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Визначає атрибути фабрики.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
