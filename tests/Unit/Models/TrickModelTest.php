<?php

namespace Tests\Unit\Models;

use App\Models\Card;
use App\Models\CardHand;
use App\Models\Discard;
use App\Models\Round;
use App\Models\Trick;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrickModelTest extends TestCase
{
    use RefreshDatabase;

    public function testCreation()
    {
        $trick = Trick::factory()->create();
        $this->assertInstanceOf(Trick::class, $trick);
    }

    public function testAttributes()
    {
        $trick = Trick::factory()->create();
        $this->assertIsInt($trick->id);
        $this->assertIsInt($trick->round_id);
        $this->assertDatabaseHas('tricks', [
            'id' => $trick->id,
            'round_id' => $trick->round_id
        ]);
    }

    public function testBelongsToRound()
    {
        $trick = Trick::factory()->create();
        $this->assertInstanceOf(Trick::class, $trick);
        $this->assertInstanceOf(Round::class, $trick->round);
        $this->assertEquals($trick->round_id, $trick->round->id);
    }

    public function testHasManyDiscards()
    {
        $trick = Trick::factory()->withFourDiscards()->create();
        $discards = $trick->discards;
        $this->assertNotNull($discards);
        $this->assertEquals(4, $discards->count());
        for ($i = 0; $i < 4; $i++) {
            $this->assertEquals($trick->id, $discards[$i]->trick_id);
        }
    }

    public function testHasManyCardHands()
    {
        $trick = Trick::factory()->withFourDiscards()->create();
        $discards = $trick->discards;
        $cardHands = $trick->cardHands;
        $this->assertNotNull($cardHands);
        $this->assertEquals(4, $cardHands->count());
        for ($i = 0; $i < 4; $i++) {
            $this->assertEquals($discards[$i]->id, $cardHands[$i]->id);
        }
    }

    public function testGetCards()
    {
        $trick = Trick::factory()->withFourDiscards()->create();
        $cards = $trick->getCards();
        $this->assertNotNull($cards);
        $this->assertEquals(4, $cards->count());
        $this->assertInstanceOf(Card::class, $cards[0]);
    }

    public function testPreviousTrick()
    {
        $round = Round::factory()->create();
        $tricks = Trick::factory()->count( 2)->create([
            'round_id' => $round->id
        ]);
        $trick1 = $tricks[0];
        $trick2 = $tricks[1];
        $this->assertEquals($trick1->id, $trick2->previousTrick()->id);
    }

    public function testPreviousTrickNull()
    {
        $round = Round::factory()->create();
        $trick = Trick::factory()->create([
            'round_id' => $round->id
        ]);
        $this->assertNull($trick->previousTrick());
    }

    public function testLeadingSuit()
    {
        $trick = Trick::factory()->create();
        $card = Card::factory()->create([
            'suit' => 'clubs',
            'rank' => 'ace'
        ]);
        $cardHand = CardHand::factory()->create([
            'card_id' => $card->id
        ]);
        $discard = Discard::factory()->create([
            'cardhand_id' => $cardHand->id,
            'trick_id' => $trick->id
        ]);
        $this->assertEquals('clubs', $trick->leadingSuit());
    }

    public function testLeadingSuitNull()
    {
        $trick = Trick::factory()->create();
        $this->assertNull($trick->leadingSuit());
    }

    public function testWinningGamePlayer()
    {
        $trick = Trick::factory()->create();
        $card1 = Card::factory()->create([
            'suit' => 'clubs',
            'rank' => 'queen',
            'value' => 12
        ]);
        $card2 = Card::factory()->create([
            'suit' => 'clubs',
            'rank' => 'ace',
            'value' => 13
        ]);
        $card3 = Card::factory()->create([
            'suit' => 'spades',
            'rank' => 'queen',
            'value' => 12
        ]);
        $card4 = Card::factory()->create([
            'suit' => 'clubs',
            'rank' => 'jack',
            'value' => 11
        ]);
        $cardHand1 = CardHand::factory()->create([
            'card_id' => $card1->id
        ]);
        $cardHand2 = CardHand::factory()->create([
            'card_id' => $card2->id
        ]);
        $cardHand3 = CardHand::factory()->create([
            'card_id' => $card3->id
        ]);
        $cardHand4 = CardHand::factory()->create([
            'card_id' => $card4->id
        ]);
        $discard1 = Discard::factory()->create([
            'cardhand_id' => $cardHand1->id,
            'trick_id' => $trick->id
        ]);
        $discard2 = Discard::factory()->create([
            'cardhand_id' => $cardHand2->id,
            'trick_id' => $trick->id
        ]);
        $discard3 = Discard::factory()->create([
            'cardhand_id' => $cardHand3->id,
            'trick_id' => $trick->id
        ]);
        $discard4 = Discard::factory()->create([
            'cardhand_id' => $cardHand4->id,
            'trick_id' => $trick->id
        ]);
        $this->assertEquals($cardHand2->gamePlayer->id, $trick->winningGamePlayer()->id);
    }

    public function testGetTrickPoints13()
    {
        $trick = Trick::factory()->create();
        $card1 = Card::factory()->create(['suit' => 'clubs', 'rank' => 'queen', 'value' => 12]);
        $card2 = Card::factory()->create(['suit' => 'clubs', 'rank' => 'ace', 'value' => 13]);
        $card3 = Card::factory()->create(['suit' => 'spades', 'rank' => 'queen', 'value' => 12]);
        $card4 = Card::factory()->create(['suit' => 'clubs', 'rank' => 'jack','value' => 11 ]);

        $cardHand1 = CardHand::factory()->create(['card_id' => $card1->id]);
        $cardHand2 = CardHand::factory()->create(['card_id' => $card2->id]);
        $cardHand3 = CardHand::factory()->create(['card_id' => $card3->id]);
        $cardHand4 = CardHand::factory()->create(['card_id' => $card4->id]);

        $discard1 = Discard::factory()->create(['cardhand_id' => $cardHand1->id, 'trick_id' => $trick->id]);
        $discard2 = Discard::factory()->create(['cardhand_id' => $cardHand2->id, 'trick_id' => $trick->id]);
        $discard3 = Discard::factory()->create(['cardhand_id' => $cardHand3->id, 'trick_id' => $trick->id]);
        $discard4 = Discard::factory()->create(['cardhand_id' => $cardHand4->id, 'trick_id' => $trick->id]);

        $this->assertEquals(13, $trick->getTrickPoints());
    }

    public function testGetTrickPoints0()
    {
        $trick = Trick::factory()->create();
        $card1 = Card::factory()->create(['suit' => 'clubs', 'rank' => 'queen', 'value' => 12]);
        $card2 = Card::factory()->create(['suit' => 'clubs', 'rank' => 'ace', 'value' => 13]);
        $card3 = Card::factory()->create(['suit' => 'diamonds', 'rank' => 'queen', 'value' => 12]);
        $card4 = Card::factory()->create(['suit' => 'clubs', 'rank' => 'jack','value' => 11 ]);

        $cardHand1 = CardHand::factory()->create(['card_id' => $card1->id]);
        $cardHand2 = CardHand::factory()->create(['card_id' => $card2->id]);
        $cardHand3 = CardHand::factory()->create(['card_id' => $card3->id]);
        $cardHand4 = CardHand::factory()->create(['card_id' => $card4->id]);

        $discard1 = Discard::factory()->create(['cardhand_id' => $cardHand1->id, 'trick_id' => $trick->id]);
        $discard2 = Discard::factory()->create(['cardhand_id' => $cardHand2->id, 'trick_id' => $trick->id]);
        $discard3 = Discard::factory()->create(['cardhand_id' => $cardHand3->id, 'trick_id' => $trick->id]);
        $discard4 = Discard::factory()->create(['cardhand_id' => $cardHand4->id, 'trick_id' => $trick->id]);

        $this->assertEquals(0, $trick->getTrickPoints());
    }

    public function testGetTrickPoints3()
    {
        $trick = Trick::factory()->create();
        $card1 = Card::factory()->create(['suit' => 'hearts', 'rank' => 'queen', 'value' => 12]);
        $card2 = Card::factory()->create(['suit' => 'hearts', 'rank' => 'ace', 'value' => 13]);
        $card3 = Card::factory()->create(['suit' => 'diamonds', 'rank' => 'queen', 'value' => 12]);
        $card4 = Card::factory()->create(['suit' => 'hearts', 'rank' => 'jack','value' => 11 ]);

        $cardHand1 = CardHand::factory()->create(['card_id' => $card1->id]);
        $cardHand2 = CardHand::factory()->create(['card_id' => $card2->id]);
        $cardHand3 = CardHand::factory()->create(['card_id' => $card3->id]);
        $cardHand4 = CardHand::factory()->create(['card_id' => $card4->id]);

        $discard1 = Discard::factory()->create(['cardhand_id' => $cardHand1->id, 'trick_id' => $trick->id]);
        $discard2 = Discard::factory()->create(['cardhand_id' => $cardHand2->id, 'trick_id' => $trick->id]);
        $discard3 = Discard::factory()->create(['cardhand_id' => $cardHand3->id, 'trick_id' => $trick->id]);
        $discard4 = Discard::factory()->create(['cardhand_id' => $cardHand4->id, 'trick_id' => $trick->id]);

        $this->assertEquals(3, $trick->getTrickPoints());
    }

    public function testGetTrickPoints16()
    {
        $trick = Trick::factory()->create();
        $card1 = Card::factory()->create(['suit' => 'hearts', 'rank' => 'queen', 'value' => 12]);
        $card2 = Card::factory()->create(['suit' => 'hearts', 'rank' => 'ace', 'value' => 13]);
        $card3 = Card::factory()->create(['suit' => 'spades', 'rank' => 'queen', 'value' => 12]);
        $card4 = Card::factory()->create(['suit' => 'hearts', 'rank' => 'jack','value' => 11 ]);

        $cardHand1 = CardHand::factory()->create(['card_id' => $card1->id]);
        $cardHand2 = CardHand::factory()->create(['card_id' => $card2->id]);
        $cardHand3 = CardHand::factory()->create(['card_id' => $card3->id]);
        $cardHand4 = CardHand::factory()->create(['card_id' => $card4->id]);

        $discard1 = Discard::factory()->create(['cardhand_id' => $cardHand1->id, 'trick_id' => $trick->id]);
        $discard2 = Discard::factory()->create(['cardhand_id' => $cardHand2->id, 'trick_id' => $trick->id]);
        $discard3 = Discard::factory()->create(['cardhand_id' => $cardHand3->id, 'trick_id' => $trick->id]);
        $discard4 = Discard::factory()->create(['cardhand_id' => $cardHand4->id, 'trick_id' => $trick->id]);

        $this->assertEquals(16, $trick->getTrickPoints());
    }
}
