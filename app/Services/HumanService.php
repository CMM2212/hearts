<?php

namespace App\Services;

use App\Models\GamePlayer;
use App\Models\Hand;
use App\Models\Trick;
use Illuminate\Support\Facades\Log;

class HumanService
{
    protected $playerService;
    protected $gameService;
    protected $roundService;

    public function __construct(PlayerService $playerService, GameService $gameService, RoundService $roundService)
    {
        $this->playerService = $playerService;
        $this->gameService = $gameService;
        $this->roundService = $roundService;
    }

    public function getData(GamePlayer $player) : array {
        $game = $player->game;
        $round = $game->rounds()->orderBy('id', 'desc')->first();
        $trick = $round->tricks()->orderBy('id', 'desc')->first();
        $state = $this->getCurrentState($game, $round, $trick);
        if ($state == 'passing')
            $history = $this->getRecentDiscardsPass($round);
        else
            $history = $this->getRecentDiscards($trick);
        $playersData = $this->getPlayerData($game, $round, $trick);
        $cardHands = $this->getPlayerCardHands($player, $round, $trick, $state);
        $hasRoundChanged = $this->hasRoundChanged($state, $round);

        return array(
            'gamePlayerId' => $player->id,
            'round' => $round,
            'state' => $state,
            'history' => $history,
            'cardHands' => $cardHands,
            'playersData' => $playersData,
            'roundChanged' => $hasRoundChanged,
        );
    }

    public function getPlayerCardHands($player, $round, $trick, $state) : array
    {
        $hand = $player->getHandForRound($round);
        if ($state == 'passing')
            $cardHands = $hand->cardHands()->whereNull('from_hand_id')->with('card')->get();
        else {
            if ($trick == null)
                return [];
            $allCardHands = $hand->cardHands()->whereDoesntHave("discard")->with('card')->get();
            $playableCardHands = $this->playerService->getPlayableCards($trick, $player);
            $playableCardHandIds = $playableCardHands->pluck('id');
            $cardHands = $allCardHands->transform(function ($cardHand) use ($playableCardHandIds) {
                $cardHand->isPlayable = $playableCardHandIds->contains($cardHand->id);
                return $cardHand;
            });
        }
        return $cardHands->sortBy(function ($cardHand) {
            $suitOrder = ['clubs', 'diamonds', 'spades', 'hearts'];
            $suit = $cardHand->card->suit;
            $suitIndex = array_search($suit, $suitOrder) + 1;
            $value = $cardHand->card->value;
            return ($suitIndex * 53 + $value);
        })->values()->toArray();
    }

//    public function getPlayerCardHandsPass($hand)
//    {
//        $cardHands = $hand->cardHands()->whereNull('from_hand_id')->with('card')->get();
//        $cardHands = $cardHands->sortBy(function ($cardHand) {
//            $suitOrder = ['clubs', 'diamonds', 'spades', 'hearts'];
//            $suit = $cardHand->card->suit;
//            $suitIndex = array_search($suit, $suitOrder) + 1;
//            $value = $cardHand->card->value;
//            return ($suitIndex * 53 + $value);
//        })->values();
//        return $cardHands;
//    }

    public function hasRoundChanged($state, $round) : bool
    {
        return ($state == 'passing' && $round->roundNumber() != 1)
            || ($this->roundService->getPassingDirection($round) === 'none'
                && ($round->tricks()->count() === 1));
    }


    public function getPlayerData($game, $round, $trick)
    {
        $players = $game->gamePlayers()->with('player')->get();
        $scores = $this->gameService->calculateGameScores($game)->toArray();
        return $players->map(function ($gamePlayer) use ($round, $trick, $scores) {
            $hand = $gamePlayer->getHandForRound($round);
            $handCount = $hand->cardHands()->whereDoesntHave('discard')->count();
            $discards = $hand->cardHands()->whereHas('discard')->with('discard')->with('card')->get();
            $score = $scores[$gamePlayer->id];

            $currentDiscard = $discards->filter(function ($cardHand) use ($trick) {
                return $cardHand->discard->trick_id == $trick->id;
            })->first();
                return [
                    'id' => $gamePlayer->id,
                    'name' => $gamePlayer->player->name,
                    'isHuman' => $gamePlayer->is_human ? true : false,
                    'discarded' => $currentDiscard->card ?? null,
                    'handCount' => $handCount,
                    'score' => $score
                ];
            })->toArray();
    }

    public function getRecentDiscards($trick)
    {
        $isFirstTrickOfRound = $trick->round->tricks->count() == 1;
        if (!$isFirstTrickOfRound) {
            // Get discards from the previous trick this round.
            $previousTrick = Trick::where('round_id', $trick->round->id)
                ->where('id', '<', $trick->id)
                ->orderBy('id', 'desc')
                ->first();
            $previousDiscards = $previousTrick->discards;
            $previousDiscardsDetailed = [];
            $humanHasPlayed = false;
            foreach ($previousDiscards as $discard) {
                if ($discard->cardHand->gamePlayer->is_human) {
                    $humanHasPlayed = true;
                    continue;
                }
                if (!$humanHasPlayed)
                    continue;
                $cardHand = $discard->cardHand;
                $cardHand->load('hand', 'card', 'gamePlayer');
                $previousDiscardsDetailed[] = $cardHand;
            }
            $previousWinner = $previousTrick->winningGamePlayer();
        } else {
            if ($this->roundService->getPassingDirection($trick->round) == 'none') {
                $previousRound = $trick->round->game->rounds()->where('id', '<', $trick->round->id)->orderBy('id', 'desc')->first();
                $previousTrick = Trick::where('round_id', $previousRound->id)
                        ->orderBy('id', 'desc')
                        ->first();
                $previousDiscards = $previousTrick->discards;
                $previousDiscardsDetailed = [];
                $humanHasPlayed = false;
                foreach ($previousDiscards as $discard) {
                    if ($discard->cardHand->gamePlayer->is_human) {
                        $humanHasPlayed = true;
                        continue;
                    }
                    if (!$humanHasPlayed)
                        continue;
                    $cardHand = $discard->cardHand;
                    $cardHand->load('hand', 'card', 'gamePlayer');
                    $previousDiscardsDetailed[] = $cardHand;
                }
                $previousWinner = $previousTrick->winningGamePlayer();
            } else {
                $previousDiscardsDetailed = [];
                $previousWinner = null;
            }
        }

        $currentlyDiscarded = $trick->discards;
        $currentlyDiscardedDetailed = [];
        foreach ($currentlyDiscarded as $discard2) {
            $cardHand = $discard2->cardHand;
            $cardHand->load('hand', 'card', 'gamePlayer');
            $currentlyDiscardedDetailed[] = $cardHand;
        }
        return [
            'previousDiscards' => $previousDiscardsDetailed,
            'previousWinner' => $previousWinner,
            'currentlyDiscarded' => $currentlyDiscardedDetailed
        ];
    }

    public function getRecentDiscardsPass($round)
    {
        if ($round->roundNumber() == 1)
            return [
                'previousDiscards' => [],
                'previousWinner' => null,
                'currentlyDiscarded' => []
            ];
        $previousRound = $round->game->rounds()->where('id', '<', $round->id)->orderBy('id', 'desc')->first();
        $previousTrick = Trick::where('round_id', $previousRound->id)
                ->orderBy('id', 'desc')
                ->first();
        $previousDiscards = $previousTrick->discards;
        $previousDiscardsDetailed = [];
        $humanHasPlayed = false;
        foreach ($previousDiscards as $discard) {
            if ($discard->cardHand->gamePlayer->is_human) {
                $humanHasPlayed = true;
                continue;
            }
            if (!$humanHasPlayed)
                continue;
            $cardHand = $discard->cardHand;
            $cardHand->load('hand', 'card', 'gamePlayer');
            $previousDiscardsDetailed[] = $cardHand;
        }
        $previousWinner = $previousTrick->winningGamePlayer();
        return [
            'previousDiscards' => $previousDiscardsDetailed,
            'previousWinner' => $previousWinner,
            'currentlyDiscarded' => []
        ];
    }

    private function getCurrentState($game, $round, $trick)
    {
        $scores = $this->gameService->calculateGameScores($game);
        $isGameOver = $this->gameService->isGameOver($scores);
        if ($isGameOver)
            return 'end';
        if ($trick == null && !$this->roundService->getPassingDirection($round) != "none") {
            return 'passing';
        }
        return 'trick';
    }
}
