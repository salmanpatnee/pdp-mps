<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Journal>
 */
class JournalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company . ' Journal',
            'issn' => $this->faker->optional()->regexify('[0-9]{4}-[0-9]{3}[0-9X]'),
            'eissn' => $this->faker->optional()->regexify('[0-9]{4}-[0-9]{3}[0-9X]'),
            'abbreviation' => $this->faker->optional()->lexify(strtoupper($this->faker->lexify('???'))),
            'description' => $this->faker->optional()->paragraphs(2, true),
            'publisher' => $this->faker->optional()->company,
            'email' => $this->faker->optional()->safeEmail,
            'website_url' => $this->faker->optional()->url,
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
