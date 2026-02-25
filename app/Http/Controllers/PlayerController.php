<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use App\Services\PlayerService;

class PlayerController extends Controller
{
    public function __construct(
        protected PlayerService $playerService
    ) {}

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return PlayerResource::collection($this->playerService->getAll());
    }

    public function show(Player $player): PlayerResource
    {
        return new PlayerResource($player->loadMissing('skills'));
    }

    public function store(StorePlayerRequest $request): \Illuminate\Http\JsonResponse
    {
        $player = $this->playerService->create($request->validated() + ['playerSkills' => $request->input('playerSkills')]);

        return (new PlayerResource($player->loadMissing('skills')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdatePlayerRequest $request, Player $player): PlayerResource
    {
        $player = $this->playerService->update($player, $request->validated() + ['playerSkills' => $request->input('playerSkills')]);

        return new PlayerResource($player->loadMissing('skills'));
    }

    public function destroy(Player $player): \Illuminate\Http\JsonResponse
    {
        $this->playerService->delete($player);
        return response()->json(['message' => 'Player deleted']);
    }
}
