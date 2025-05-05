<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Round;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GamePlayer>
 */
class GamePlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_id' => Game::factory()->create()->id,
            'player_id' => Player::factory()->create()->id,
            'seat_number' => $this->faker->numberBetween(1, 4),
            'is_human' => false
        ];
    }

    /**
     * State: Attach hands to the GamePlayer.
     */
    public function withHands(): static
    {
        return $this->afterCreating(function (GamePlayer $gamePlayer) {
            // Create a Round related to the Game
            $round = Round::factory()->create(['game_id' => $gamePlayer->game_id]);

            // Create 4 Hands for this GamePlayer in the Round
            for ($i = 0; $i < 4; $i++) {
                Hand::factory()->create([
                    'gameplayer_id' => $gamePlayer->id,
                    'round_id' => $round->id,
                ]);
            }
        });
    }
}
