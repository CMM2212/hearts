<?php

namespace Database\Factories;

use App\Models\Card;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    protected $model = Card::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (Card::count() >= 52) {
            throw new Exception('Too many cards');
        }

        $suits = ['clubs', 'diamonds', 'hearts', 'spades'];
        $ranks = ['2','3','4','5','6','7','8','9','10','jack','queen','king','ace'];
        $values = [2,3,4,5,6,7,8,9,10,11,12,13,14];

        do {
            $suit = $this->faker->randomElement($suits);
            $rank = $this->faker->randomElement($ranks);
            $value = $values[array_search($rank, $ranks)];
        } while (Card::where('suit', $suit)->where('rank', $rank)->exists());

        return [
            'suit' => $suit,
            'rank' => $rank,
            'value' => $value
        ];
    }
}
