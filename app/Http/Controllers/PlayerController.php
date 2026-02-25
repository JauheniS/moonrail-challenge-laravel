<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function index()
    {
        return PlayerResource::collection(Player::with('skills')->get());
    }

    public function show(Player $player)
    {
        return new PlayerResource($player->load('skills'));
    }

    public function store(StorePlayerRequest $request)
    {
        $player = DB::transaction(function () use ($request) {
            $player = Player::create($request->validated());

            $player->skills()->createMany($request->input('playerSkills'));

            return $player;
        });

        return (new PlayerResource($player->load('skills')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdatePlayerRequest $request, Player $player)
    {
        $player = DB::transaction(function () use ($player, $request) {
            $player->update($request->validated());

            $player->skills()->delete();
            $player->skills()->createMany($request->input('playerSkills'));

            return $player;
        });

        return new PlayerResource($player->load('skills'));
    }

    public function destroy(Player $player)
    {
        $player->delete();
        return response()->json(['message' => 'Player deleted']);
    }
}
