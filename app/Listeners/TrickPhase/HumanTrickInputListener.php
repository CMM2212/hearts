<?php

namespace App\Listeners\TrickPhase;

use App\Events\TrickPhase\HumanTrickInputEvent;
use App\Services\GameOrchestrationService;
use App\Services\GameService;
use App\Services\HumanService;
use App\Services\PlayerService;

class HumanTrickInputListener
{
    protected $playerService;
    protected $gameService;
    protected $humanService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PlayerService $playerService, GameService $gameService, HumanService $humanService)
    {
        $this->playerService = $playerService;
        $this->gameService = $gameService;
        $this->humanService = $humanService;
    }

    /**
     * Handle the event.
     *
     * @param  HumanTrickInputEvent  $event
     * @return void
     */
    public function handle(HumanTrickInputEvent $event)
    {
//        $trick= $event->trick;
//        $player = $event->player;
//        $round = $trick->round;
//        $game = $round->game;
//        $hand = $player->hands()->where('round_id', $round->id)->first();
//
//        $this->humanService->updateGameState($trick, $player, $hand);
//        $playersData = $this->humanService->getPlayerData($game, $round, $trick);
//        $cardHands = $this->humanService->getPlayerCardHands($player, $trick, $hand);
//        $hasRoundChanged = $this->humanService->hasRoundChangedTrick($trick);
//
//        $isFirstTrick = $round->tricks()->count() == 1;
//
//
//        $history = $this->humanService->getRecentDiscards($trick);
//        session()->put("state", "trick");
//        session()->put("data", [
//            'game' => $game,
//            'round' => $round,
//            'player' => $player,
//            'hand' => $hand,
//            'cardHands' => $cardHands->toArray(),
//            'playersData' => $playersData,
//            'roundChanged' => $hasRoundChanged ? true : false,
//            'history' => $history,
//            'isFirstTrick' => $isFirstTrick ? true : false
//        ]);
    }
}
