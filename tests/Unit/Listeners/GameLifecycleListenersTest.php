<?php

namespace Listeners;

use App\Events\GameLifecycle\EndGameEvent;
use App\Events\GameLifecycle\EndPassingEvent;
use App\Events\GameLifecycle\EndRoundEvent;
use App\Events\GameLifecycle\EndTrickPhaseEvent;
use App\Events\GameLifecycle\StartGameEvent;
use App\Events\GameLifecycle\StartPassingEvent;
use App\Events\GameLifecycle\StartRoundEvent;
use App\Events\GameLifecycle\StartTrickPhaseEvent;
use App\Listeners\GameLifecycle\EndGameListener;
use App\Listeners\GameLifecycle\EndPassingListener;
use App\Listeners\GameLifecycle\EndRoundListener;
use App\Listeners\GameLifecycle\EndTrickPhaseListener;
use App\Listeners\GameLifecycle\StartGameListener;
use App\Listeners\GameLifecycle\StartPassingListener;
use App\Listeners\GameLifecycle\StartRoundListener;
use App\Listeners\GameLifecycle\StartTrickPhaseListener;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Round;
use App\Models\Trick;
use App\Services\GameOrchestrationService;
use App\Services\GameService;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GameLifecycleListenersTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->round = Mockery::mock(Round::class);
        $this->game = Mockery::mock(Game::class);
        $this->trick = Mockery::mock(Trick::class);
        $this->gameService = Mockery::mock(GameService::class);
        $this->orchestrator = Mockery::mock(GameOrchestrationService::class);
    }

    public function testStartGameListener()
    {
        $playerId = 1;
        $gamePlayerId = 4;
        $gamePlayer = Mockery::mock(GamePlayer::class);
        $gamePlayer->shouldReceive('getAttribute')->with('id')->andReturn($gamePlayerId);
        $this->orchestrator = Mockery::mock(GameOrchestrationService::class);
        $this->orchestrator->shouldReceive('startGameSequence')->once()->with($playerId)
            ->andReturn($gamePlayer);
        $listener = new StartGameListener($this->orchestrator);
        $event = new StartGameEvent($playerId);
        $listener->handle($event);
        $this->assertEquals($playerId, $event->playerId);
        $this->assertEquals($gamePlayerId, $event->gamePlayer->id);
    }

    public function testStartRoundListener()
    {
        $this->game = Mockery::mock(Game::class);
        $this->orchestrator = Mockery::mock(GameOrchestrationService::class);
        $this->orchestrator->shouldReceive('startRoundSequence')->once()->with($this->game);
        $listener = new StartRoundListener($this->orchestrator);
        $event = new StartRoundEvent($this->game);
        $listener->handle($event);
        $this->assertEquals($this->game, $event->game);
    }

    public function testStartPassingEventListener()
    {
        $this->round = Mockery::mock(Round::class);
        $this->orchestrator = Mockery::mock(GameOrchestrationService::class);
        $this->orchestrator->shouldReceive('startPassingSequence')->once()->with($this->round);
        $listener = new StartPassingListener($this->orchestrator);
        $event = new StartPassingEvent($this->round);
        $listener->handle($event);
        $this->assertEquals($this->round, $event->round);
    }

    public function testEndPassingEvent()
    {
        $this->round = Mockery::mock(Round::class);
        $this->orchestrator = Mockery::mock(GameOrchestrationService::class);
        $this->orchestrator->shouldReceive('endPassingSequence')->once()->with($this->round);
        $listener = new EndPassingListener($this->orchestrator);
        $event = new EndPassingEvent($this->round);
        $listener->handle($event);
        $this->assertEquals($this->round, $event->round);
    }

    public function testStartTrickPhaseListener()
    {
        $this->round = Mockery::mock(Round::class);
        $this->orchestrator = Mockery::mock(GameOrchestrationService::class);
        $this->orchestrator->shouldReceive('startTrickPhaseSequence')->once()->with($this->round);
        $listener = new StartTrickPhaseListener($this->orchestrator);
        $event = new StartTrickPhaseEvent($this->round);
        $listener->handle($event);
        $this->assertEquals($this->round, $event->round);
    }

    public function testEndTrickPhaseListener()
    {
        $this->trick = Mockery::mock(Trick::class);
        $this->orchestrator = Mockery::mock(GameOrchestrationService::class);
        $this->orchestrator->shouldReceive('startEndTrickPhaseSequence')->once()->with($this->trick);
        $listener = new EndTrickPhaseListener($this->orchestrator);
        $event = new EndTrickPhaseEvent($this->trick);
        $listener->handle($event);
        $this->assertEquals($this->trick, $event->trick);
    }

    public function testEndRoundListener()
    {
        $this->round = Mockery::mock(Round::class);
        $this->orchestrator = Mockery::mock(GameOrchestrationService::class);
        $this->orchestrator->shouldReceive('startEndRoundSequence')->once()->with($this->round);
        $listener = new EndRoundListener($this->orchestrator);
        $event = new EndRoundEvent($this->round);
        $listener->handle($event);
        $this->assertEquals($this->round, $event->round);
    }

//    public function testEndGameListener()
//    {
//        $game = Mockery::mock(Game::class);
//        $gamePlayers = Mockery::mock(Collection::class);
//        $player1 = (object) ['id' => 1, 'player' => (object) ['name' => 'Player 1'], 'is_human' => true];
//        $player2 = (object) ['id' => 2, 'player' => (object) ['name' => 'Player 2'], 'is_human' => false];
//        $gamePlayersData = collect([$player1, $player2]);
//        $scores = collect([
//            1 => 50,
//            2 => 30
//        ]);
//
//        $orchestrator = Mockery::mock(GameOrchestrationService::class);
//        $gameService = Mockery::mock(GameService::class);
//
//        $orchestrator->shouldReceive('startEndGameSequence')->once()->with($game, $scores);
//        $gameService->shouldReceive('calculateGameScores')->once()->with($game)->andReturn(collect($scores));
//
//        $game->shouldReceive('gamePlayers')->andReturnSelf();
//        $game->shouldReceive('with')->with('player')->andReturnSelf();
//        $game->shouldReceive('get')->andReturn($gamePlayersData);
//
//        $gamePlayers->shouldReceive('map')->andReturn(collect([
//            [
//                'id' => 1,
//                'name' => 'Player 1',
//                'isHuman' => true,
//                'discarded' => null,
//                'handCount' => 0,
//                'score' => 50,
//            ],
//            [
//                'id' => 2,
//                'name' => 'Player 2',
//                'isHuman' => false,
//                'discarded' => null,
//                'handCount' => 0,
//                'score' => 30,
//            ]
//        ]));
//        $listener = new EndGameListener($orchestrator, $gameService);
//        $event = new EndGameEvent($game, $scores);
//        $listener->handle($event);
//
//        // Assert session values
//        $this->assertEquals('end', $_SESSION["state"]);
//        $this->assertEquals([
//            'gameOver' => true,
//            'playersData' => [
//                [
//                    'id' => 1,
//                    'name' => 'Player 1',
//                    'isHuman' => true,
//                    'discarded' => null,
//                    'handCount' => 0,
//                    'score' => 50,
//                ],
//                [
//                    'id' => 2,
//                    'name' => 'Player 2',
//                    'isHuman' => false,
//                    'discarded' => null,
//                    'handCount' => 0,
//                    'score' => 30,
//                ]
//            ],
//            'cardHands' => []
//        ], $_SESSION["data"]);
//    }

    public function testEndGameListener()
    {
        $scores = collect([
            1 => 122,
            2 => 54,
            3 => 76,
            4 => 98,
        ]);
        $gamePlayers = collect([
            (object)[
                'id' => 1,
                'player' => (object)['name' => 'Human'],
                'is_human' => true,
            ],
            (object)[
                'id' => 2,
                'player' => (object)['name' => 'Comp1'],
                'is_human' => false,
            ],
            (object)[
                'id' => 3,
                'player' => (object)['name' => 'Comp2'],
                'is_human' => false,
            ],
            (object)[
                'id' => 4,
                'player' => (object)['name' => 'Comp3'],
                'is_human' => false,
            ],
        ]);

        $this->orchestrator
            ->shouldReceive('startEndGameSequence')
            ->once()
            ->with($this->game, $scores);

        $listener = new EndGameListener($this->orchestrator, $this->gameService);

        $event = new EndGameEvent($this->game, $scores);
        $listener->handle($event);

    }
}
