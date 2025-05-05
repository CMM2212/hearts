<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Hand;
use App\Models\Round;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Round>
 */
class RoundFactory extends Factory
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
        ];
    }

    /**
     * State: Add Hands to the Round for all GamePlayers.
     */
    public function withHands(): static
    {
        return $this->afterCreating(function (Round $round) {
            // Create a Game with GamePlayers
            $game = Game::factory()->withGamePlayers()->create();

            // Update the Round's game_id to match the created Game
            $round->game_id = $game->id;
            $round->save();

            // Create Hands for each GamePlayer in the Game
            foreach ($game->gamePlayers as $gamePlayer) {
                Hand::factory()->create([
                    'round_id' => $round->id,
                    'gameplayer_id' => $gamePlayer->id,
                ]);
            }
        });
    }
}
