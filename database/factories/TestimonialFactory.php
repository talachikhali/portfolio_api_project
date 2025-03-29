<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
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
            'name' => fake()->name(),
            'role' => fake()->jobTitle(),
            'feedback' => fake()->paragraph()
        ];
    }
}
