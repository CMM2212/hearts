<?php

namespace Tests\Unit\Models;

use App\Models\GamePlayer;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerModelTest extends TestCase
{
    use RefreshDatabase;
    public function testCreation()
    {
        $player = Player::factory()->create();
        $this->assertInstanceOf(Player::class, $player);
        $this->assertDatabaseHas('players', ['id' => $player->id]);
    }

    public function testCreateWithName()
    {
        $player = Player::factory()->create(['name' => 'Laravel']);
        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals('Laravel', $player->name);
        $this->assertDatabaseHas('players', ['id' => $player->id]);
        $this->assertDatabaseHas('players', ['name' => 'Laravel']);
    }

    public function testAttributes()
    {
        $player = Player::factory()->create();
        $this->assertIsInt($player->id);
        $this->assertIsString($player->name);
    }

    public function testHasManyGamePlayers()
    {
        $player = Player::factory()->withGamePlayers()->create();
        $this->assertInstanceOf(Player::class, $player);
        $this->assertCount(1, $player->gamePlayers);
        $this->assertDatabaseHas('game_player', ['player_id' => $player->id]);
        $this->assertInstanceOf(GamePlayer::class, $player->gamePlayers[0]);
    }
}
