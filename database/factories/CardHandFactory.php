<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\Hand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CardHand>
 */
class CardHandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hand_id' => Hand::factory(),
            'card_id' => Card::factory(),
            'from_hand_id' => null,
        ];
    }
}
