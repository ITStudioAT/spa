<?php

namespace Itstudioat\Spa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Itstudioat\spa\Models\MyModel;

class MyModelFactory extends Factory
{
    protected $model = MyModel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
