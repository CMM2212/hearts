<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * State: Add GamePlayers to the Game.
     */
    public function withGamePlayers(): static
    {
        return $this->afterCreating(function (Game $game) {
            $players = Player::factory()->count(4)->create(); // Create 4 players
            $i = 1;
            foreach ($players as $player) {
                GamePlayer::factory()->create([
                    'game_id' => $game->id,
                    'player_id' => $player->id,
                    'seat_number' => $i++,
                ]);
            }
        });
    }

    /**
     * State: Add three rounds to the Game.
     */
    public function withThreeRounds(): static
    {
        return $this->afterCreating(function (Game $game) {
            for ($i = 0; $i < 3; $i++) {
                $game->rounds()->create(); // Create a round for the game
            }
        });
    }

    /**
     * State: Add one round to the Game.
     */
    public function withOneRound(): static
    {
        return $this->afterCreating(function (Game $game) {
            $game->rounds()->create(); // Create one round for the game
        });
    }
}
