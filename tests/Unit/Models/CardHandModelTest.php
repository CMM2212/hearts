<?php

namespace Tests\Unit\Models;

use App\Models\Card;
use App\Models\CardHand;
use App\Models\Discard;
use App\Models\GamePlayer;
use App\Models\Hand;
use App\Models\Trick;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardHandModelTest extends TestCase
{
    use RefreshDatabase;

    public function testCreation()
    {
        $cardHand = CardHand::factory()->create();
        $this->assertInstanceOf(CardHand::class, $cardHand);
        $this->assertDatabaseHas('cardhands', ['id' => $cardHand->id]);
    }

    public function testAttributes()
    {
        $cardHand = CardHand::factory()->create();
        $this->assertIsInt($cardHand->id);
        $this->assertIsInt($cardHand->hand_id);
        $this->assertIsInt($cardHand->card_id);
        $this->assertNull($cardHand->from_hand_id);
    }

    public function testBelongsToHand()
    {
        $cardHand = CardHand::factory()->create();
        $this->assertInstanceOf(CardHand::class, $cardHand);
        $this->assertInstanceOf(Hand::class, $cardHand->hand);
        $this->assertEquals($cardHand->hand_id, $cardHand->hand->id);
    }

    public function testBelongsToCard()
    {
        $cardHand = CardHand::factory()->create();
        $this->assertInstanceOf(CardHand::class, $cardHand);
        $this->assertInstanceOf(Card::class, $cardHand->card);
        $this->assertEquals($cardHand->card_id, $cardHand->card->id);
    }

    public function testHasOneDiscard()
    {
        $trick = Trick::factory()->create();
        $cardHand = CardHand::factory()->create();
        $this->assertNull($cardHand->discard);
        $discard = $trick->discards()->create(['cardhand_id' => $cardHand->id]);
        $cardHand->refresh();
        $this->assertInstanceOf(Discard::class, $cardHand->discard);
        $this->assertEquals($discard->id, $cardHand->discard->id);
    }

    public function testBelongsToGamePlayer()
    {
        $gamePlayer = GamePlayer::factory()->create();
        $hand = Hand::factory()->create([
            'gameplayer_id' => $gamePlayer->id
            ]
        );
        $cardHand = CardHand::factory()->create([
            'hand_id' => $hand->id
        ]);
        $gamePlayerResult = $cardHand->gamePlayer;
        $this->assertInstanceOf(GamePlayer::class, $gamePlayerResult);
        $this->assertEquals($gamePlayer->id, $gamePlayerResult->id);
    }
}
