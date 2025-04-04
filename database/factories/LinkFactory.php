<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => function(){
                return \App\Models\User::inRandomOrder()->first()->id; 
            },
            'platform' => fake()->randomElement(['facebook' , 'instagram' , 'X' , 'LinkedIn' , 'GitHub' ]),
            'link' => fake()->url(),
            'icon' => null
        ];
    }
}
