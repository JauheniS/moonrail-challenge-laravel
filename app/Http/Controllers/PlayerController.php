<?php

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

            $newSkills = collect($request->input('playerSkills'));
            $skillNames = $newSkills->pluck('skill')->toArray();

            $player->skills()->whereNotIn('skill', $skillNames)->delete();

            foreach ($newSkills as $skillData) {
                $player->skills()->updateOrCreate(
                    ['skill' => $skillData['skill']],
                    ['value' => $skillData['value']]
                );
            }

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
