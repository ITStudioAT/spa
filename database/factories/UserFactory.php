<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'last_name' => $this->faker->lastName(),
            'first_name' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'email_verified_at' => $this->faker->optional(0.9)->dateTimeThisYear(),
            'is_2fa' => $this->faker->boolean(60),
            'is_active' => $this->faker->boolean(90),
            'confirmed_at' => $this->faker->optional(0.9)->dateTimeThisYear(),
        ];
    }
}
