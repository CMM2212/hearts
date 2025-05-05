<?php

namespace Database\Factories;

use App\Models\CardHand;
use App\Models\Game;
use App\Models\Hand;
use App\Models\Round;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hand>
 */
class HandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $game = Game::factory()->withGamePlayers()->create();
        $round = Round::factory()->create([
            'game_id' => $game->id
        ]);
        $gamePlayer = $game->gamePlayers()->first();
        return [
            'round_id' => $round->id,
            'gameplayer_id' => $gamePlayer->id,
        ];
    }

    /**
     * State: Add 13 CardHands to the Hand.
     */
    public function withCardHands(): static
    {
        return $this->afterCreating(function (Hand $hand) {
            for ($i = 0; $i < 13; $i++) {
                CardHand::factory()->create(['hand_id' => $hand->id]);
            }
        });
    }

    /**
     * State: Add 10 CardHands to the Hand.
     */
    public function with10CardHands(): static
    {
        return $this->afterCreating(function (Hand $hand) {
            for ($i = 0; $i < 10; $i++) {
                CardHand::factory()->create(['hand_id' => $hand->id]);
            }
        });
    }

    /**
     * State: Add 16 CardHands to the Hand.
     */
    public function with16CardHands(): static
    {
        return $this->afterCreating(function (Hand $hand) {
            for ($i = 0; $i < 16; $i++) {
                CardHand::factory()->create(['hand_id' => $hand->id]);
            }
        });
    }
}
