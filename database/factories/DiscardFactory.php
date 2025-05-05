<?php

namespace Database\Factories;

use App\Models\CardHand;
use App\Models\Trick;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discard>
 */
class DiscardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cardhand_id' => CardHand::factory()->create()->id,
            'trick_id' => Trick::factory()->create()->id
        ];
    }
}
