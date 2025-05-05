<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }

    /**
     * State: Attach a GamePlayer to the Player.
     */
    public function withGamePlayers(): static
    {
        return $this->afterCreating(function (Player $player) {
            // Create a Game
            $game = Game::factory()->create();

            // Create a GamePlayer associated with this Player and Game
            GamePlayer::factory()->create([
                'game_id' => $game->id,
                'player_id' => $player->id,
                'seat_number' => 1,
            ]);
        });
    }
}
