<?php

namespace Database\Factories;

use App\Models\Discard;
use App\Models\Round;
use App\Models\Trick;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trick>
 */
class TrickFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'round_id' => Round::factory()->create()->id, // Link to a related Round
        ];
    }

    /**
     * State: Add one Discard to the Trick.
     */
    public function withOneDiscard(): static
    {
        return $this->afterCreating(function (Trick $trick) {
            $discard = Discard::factory()->create([
                'trick_id' => $trick->id,
            ]);
            $trick->discards()->save($discard);
        });
    }

    /**
     * State: Add four Discards to the Trick.
     */
    public function withFourDiscards(): static
    {
        return $this->afterCreating(function (Trick $trick) {
            Discard::factory()->count(4)->create([
                'trick_id' => $trick->id,
            ]);
        });
    }
}
