<?php

namespace Tests\Unit\Models;

use App\Models\Card;
use App\Models\Game;
use App\Models\Hand;
use App\Models\Round;
use App\Models\Trick;
use Database\Seeders\EmptyTrickSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoundModelTest extends TestCase
{
    use RefreshDatabase;

    public function testCreation()
    {
        $round = Round::factory()->create();
        $this->assertInstanceOf(Round::class, $round);
        $this->assertDatabaseHas('rounds', ['id' => $round->id]);
    }

    public function testAttributes()
    {
        $round = Round::factory()->create();
        $this->assertIsInt($round->id);
        $this->assertIsInt($round->game_id);
    }

    public function testRoundNumber()
    {
        Game::factory()->count(2)->create();
        $game = Game::factory()->create();
        $rounds = Round::factory()->count(4)->create(['game_id' => $game->id]);
        $this->assertCount(4, $rounds);
        $this->assertEquals(1, $rounds[0]->roundNumber());
        $this->assertEquals(2, $rounds[1]->roundNumber());
        $this->assertEquals(3, $rounds[2]->roundNumber());
        $this->assertEquals(4, $rounds[3]->roundNumber());
    }

    public function testBelongsToGame()
    {
        $game = Game::factory()->create();
        $round = Round::factory()->create(['game_id' => $game->id]);
        $this->assertInstanceOf(Game::class, $round->game);
        $this->assertEquals($game->id, $round->game->id);
    }

    public function testHasManyHands()
    {

        $round = Round::factory()->withHands()->create();
        $this->assertInstanceOf(Round::class, $round);
        $this->assertCount(4, $round->hands);
        $this->assertDatabaseHas('hands', ['round_id' => $round->id]);
        $this->assertInstanceOf(Hand::class, $round->hands[0]);
    }

    public function testCreateTrick()
    {
        $round = Round::factory()->create();
        $trick = $round->tricks()->create();
        $this->assertInstanceOf(Trick::class, $trick);
        $this->assertDatabaseHas('tricks', ['id' => $trick->id]);
        $this->assertDatabaseHas('tricks', ['round_id' => $round->id]);
        $this->assertEquals($round->id, $trick->round_id);
    }

    public function testIsHeartsBrokenFirstTrick()
    {
        $round = Round::factory()->create();
        $round->tricks()->create();
        $this->assertFalse($round->isHeartsBroken());
    }

    public function testIsHeartsBrokenQueenOfSpades()
    {
        $this->seed(EmptyTrickSeeder::class);
        $round = Round::first();
        $hand = $round->hands()->first();
        $queenOfSpades = Card::where('suit', 'spades')->where('rank', 'queen')->first();
        $cardHand = $hand->cardHands()->create(['card_id' => $queenOfSpades->id]);
        $tricks = Trick::factory()->count( 2)->create(['round_id' => $round->id]);
        $lastTrick = $tricks->last();
        $discard = $lastTrick->discards()->create(['cardhand_id' => $cardHand->id]);
        $this->assertTrue($round->isHeartsBroken());
    }

    public function testIsHeartsBrokenOtherCard()
    {
        $this->seed(EmptyTrickSeeder::class);
        $round = Round::first();
        $hand = $round->hands()->first();
        $queenOfClubs = Card::where('suit', 'clubs')->where('rank', 'queen')->first();
        $cardHand = $hand->cardHands()->create(['card_id' => $queenOfClubs->id]);
        $tricks = Trick::factory()->count(2)->create(['round_id' => $round->id]);
        $lastTrick = $tricks->last();
        $discard = $lastTrick->discards()->create(['cardhand_id' => $cardHand->id]);
        $this->assertFalse($round->isHeartsBroken());
    }

    public function testIsHeartsBrokenHearts()
    {
        $this->seed(EmptyTrickSeeder::class);
        $round = Round::first();
        $hand = $round->hands()->first();
        $twoOfHearts = Card::where('suit', 'hearts')->where('rank', '2')->first();
        $cardHand = $hand->cardHands()->create(['card_id' => $twoOfHearts->id]);
        $tricks = Trick::factory()->count(2)->create(['round_id' => $round->id]);
        $lastTrick = $tricks->last();
        $discard = $lastTrick->discards()->create(['cardhand_id' => $cardHand->id]);
        $this->assertTrue($round->isHeartsBroken());
    }
}
