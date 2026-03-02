<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class TransacoesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tr_us_id' => User::factory(),
            'tr_valor' => $this->faker->randomFloat(2, 10, 1000),
            'tr_tipo' => $this->faker->randomElement(['deposito', 'transferencia']),
            'tr_descricao' => $this->faker->sentence(),
            'created_at' => now(),
        ];
    }
}
