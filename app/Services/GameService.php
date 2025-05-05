<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Collection;

class GameService
{
    protected $roundService;

    public function __construct(RoundService $roundService)
    {
        $this->roundService = $roundService;
    }

    public function createGame(int $playerId): array
    {
        $game = Game::create();
        $playerIds = [1, 2, 3, $playerId];
        $seatNumber = 1;
        $humanGamePlayer = null;

        foreach ($playerIds as $id)
        {
            $isHuman = $playerId === $id;
            $gamePlayer = $game->gamePlayers()->create([
                'player_id' => $id,
                'seat_number' => $seatNumber++,
                'is_human' => $isHuman
            ]);

            if ($isHuman)
                $humanGamePlayer = $gamePlayer;

        }
        return [
            'game' => $game,
            'humanGamePlayer' => $humanGamePlayer
        ];
    }

    public function calculateGameScores(Game $game): Collection
    {
        $players = $game->gamePlayers;
        $playerScores = $players->pluck('id')->flip()->map(function ($id) {
            return 0;
        });
        foreach ($game->rounds as $round)
        {
            $roundScores = $this->roundService->calculateRoundScores($round);
            foreach ($roundScores as $playerId => $score)
            {
                $playerScores[$playerId] += $score;
            }
        }
        return $playerScores;
    }

    public function isGameOver(Collection $scores): bool
    {
        foreach ($scores as $score)
        {
            if ($score >= 100)
                return true;
        }
        return false;
    }
}
