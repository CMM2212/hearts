<?php

namespace Tests\Unit\Models;

use App\Models\Card;
use App\Models\CardHand;
use App\Models\Discard;
use App\Models\Trick;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscardModelTest extends TestCase
{
    use RefreshDatabase;

    public function testCreation()
    {
        $discard = Discard::factory()->create();
        $this->assertInstanceOf(Discard::class, $discard);
    }

    public function testAttributes()
    {
        $discard = Discard::factory()->create();
        $this->assertIsInt($discard->id);
        $this->assertIsInt($discard->cardhand_id);
        $this->assertIsInt($discard->trick_id);
        $this->assertDatabaseHas('discards', [
            'id' => $discard->id,
            'cardhand_id' => $discard->cardhand_id,
            'trick_id' => $discard->trick_id
        ]);
    }

    public function testBelongsToCardHand()
    {
        $discard = Discard::factory()->create();
        $this->assertInstanceOf(Discard::class, $discard);
        $this->assertInstanceOf(CardHand::class, $discard->cardhand);
        $this->assertEquals($discard->cardhand_id, $discard->cardhand->id);
    }

    public function testBelongsToTrick()
    {
        $discard = Discard::factory()->create();
        $this->assertInstanceOf(Discard::class, $discard);
        $this->assertInstanceOf(Trick::class, $discard->trick);
        $this->assertEquals($discard->trick_id, $discard->trick->id);
    }

    public function testHasCard()
    {
        $discard = Discard::factory()->create();
        $card = $discard->cardHand->card;
        $this->assertNotNull($card);
        $this->assertInstanceOf(Card::class, $card);
    }
}
