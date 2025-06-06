<?php

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Hand;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamePlayerModelTest extends TestCase
{
    use RefreshDatabase;

    public function testCreation()
    {
        $gamePlayer = GamePlayer::factory()->create();
        $this->assertInstanceOf(GamePlayer::class, $gamePlayer);
        $this->assertDatabaseHas('game_player', ['id' => $gamePlayer->id]);
    }

    public function testAttributes()
    {
        $gamePlayer = GamePlayer::factory()->create();
        $this->assertIsInt($gamePlayer->id);
        $this->assertIsInt($gamePlayer->game_id);
        $this->assertIsInt($gamePlayer->player_id);
        $this->assertIsInt($gamePlayer->seat_number);
        $this->assertIsBool($gamePlayer->is_human);
    }

    public function testDefaultAttributes()
    {
        $gamePlayer = GamePlayer::factory()->create();
        $this->assertFalse($gamePlayer->is_human);
    }

    public function testIsHuman()
    {
        $gamePlayer = GamePlayer::factory()->create(['is_human' => true]);
        $this->assertTrue($gamePlayer->is_human);
        $gamePlayer = GamePlayer::factory()->create(['is_human' => false]);
        $this->assertFalse($gamePlayer->is_human);
    }

    public function testBelongsToGame()
    {
        $game =Game::factory()->create();
        $gamePlayer = GamePlayer::factory()->create(['game_id' => $game->id]);
        $this->assertInstanceOf(Game::class, $gamePlayer->game);
        $this->assertEquals($game->id, $gamePlayer->game->id);
    }

    public function testBelongsToPlayer()
    {
        $player = Player::factory()->create();
        $gamePlayer = GamePlayer::factory()->create(['player_id' => $player->id]);
        $this->assertInstanceOf(Player::class, $gamePlayer->player);
        $this->assertEquals($player->id, $gamePlayer->player->id);
    }

    public function testHasManyHands()
    {
        $gamePlayer = GamePlayer::factory()->withHands()->create();
        $this->assertInstanceOf(GamePlayer::class, $gamePlayer);
        $this->assertCount(4, $gamePlayer->hands);
        $this->assertDatabaseHas('hands', ['gameplayer_id' => $gamePlayer->id]);
        $this->assertInstanceOf(Hand::class, $gamePlayer->hands[0]);
    }

    public function testGetHandForRound()
    {
        $gamePlayer = GamePlayer::factory()->withHands()->create();
        $round = $gamePlayer->game->rounds->first();
        $hand = $gamePlayer->getHandForRound($round);
        $this->assertInstanceOf(Hand::class, $hand);
        $this->assertEquals($round->id, $hand->round_id);
        $this->assertEquals($gamePlayer->id, $hand->gameplayer_id);
    }
}
