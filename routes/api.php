<?php

use App\Events\GameLifecycle\StartGameEvent;
use App\Events\PassingPhase\PlayerPassInputtedEvent;
use App\Events\TrickPhase\PlayerTrickInputtedEvent;
use App\Models\CardHand;
use App\Models\GamePlayer;
use App\Models\Player;
use App\Models\Round;
use App\Models\Trick;
use App\Services\GameService;
use App\Services\HumanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

//Route::get('/player/id', function (Request $request) {
//    $playerId = session('gameState.userId');
//
//    return response()->json([
//        'success' => $playerId !== null,
//        'userId' => $playerId
//    ]);
//});

Route::post('/player/login', function(Request $request) {
    $username = $request->input('username');
    $player = Player::firstOrCreate(["name" => $username]);

    Log::info('API Route Hit: /player/login {username: ' . $username . '} {playerId: ' . $player->id . '}');

    return response()->json([
        'success' => true,
        'userId' => $player->id
    ]);
});

Route::post('/game/start', function (Request $request, HumanService $humanService, GameService $gameService) {
    $playerId = $request->input('playerId', 5);
    $event = new StartGameEvent($playerId);
    event($event);
    $gamePlayer = $event->gamePlayer;
    $data = $humanService->getData($gamePlayer);
    return response()->json(
        $data
    );
});

Route::post('/game/pass', function (Request $request, HumanService $humanService) {
    Log::info('API Route Hit: /game/pass');
    Log::info('Request Data: ', $request->all());
    $playerId = $request->input('playerId');
    $cardHandIds = $request->input('cards', []);

    $player = GamePlayer::find($playerId);
    $cardHands = CardHand::whereIn('id', $cardHandIds)->get();
    $round = $cardHands->first()->hand->round;

    event(new PlayerPassInputtedEvent($round, $player, $cardHands));

    $data = $humanService->getData($player);

    return response()->json(
        $data
    );
});

Route::post('/game/play', function (Request $request, HumanService $humanService) {
    Log::info('API Route Hit: /game/play');
    Log::info('Request Data: ', $request->all());
    $playerId = $request->input('playerId');
    $cardHandId = $request->input('card');

    $player = GamePlayer::find($playerId);
    $cardHand = CardHand::find($cardHandId);
    $trick = $cardHand->hand->round->tricks()->latest()->first();

    event(new PlayerTrickInputtedEvent($trick, $player, $cardHand));

    $data = $humanService->getData($player);
    return response()->json(
        $data
    );
});
