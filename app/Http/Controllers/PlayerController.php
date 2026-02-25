<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::all();
        return response()->json($players);
    }

    public function show($id)
    {
        $player = Player::find($id);
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }
        return response()->json($player);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'position' => 'required|string|in:defender,midfielder,forward',
            'playerSkills' => 'required|array|min:1',
            'playerSkills.*.skill' => 'required|string|in:defense,attack,speed,strength,stamina',
            'playerSkills.*.value' => 'required|integer',
        ]);

        return DB::transaction(function () use ($validated) {
            $player = Player::create([
                'name' => $validated['name'],
                'position' => $validated['position'],
            ]);

            foreach ($validated['playerSkills'] as $skillData) {
                $player->skills()->create([
                    'skill' => $skillData['skill'],
                    'value' => $skillData['value'],
                ]);
            }

            return response()->json($player->refresh(), 201);
        });
    }

    public function update(Request $request, $id)
    {
        $player = Player::find($id);
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'position' => 'required|string|in:defender,midfielder,forward',
            'playerSkills' => 'required|array|min:1',
            'playerSkills.*.skill' => 'required|string|in:defense,attack,speed,strength,stamina',
            'playerSkills.*.value' => 'required|integer',
        ]);

        return DB::transaction(function () use ($player, $validated) {
            $player->update([
                'name' => $validated['name'],
                'position' => $validated['position'],
            ]);

            $player->skills()->delete();
            foreach ($validated['playerSkills'] as $skillData) {
                $player->skills()->create([
                    'skill' => $skillData['skill'],
                    'value' => $skillData['value'],
                ]);
            }

            return response()->json($player->refresh());
        });
    }

    public function destroy($id)
    {
        $player = Player::find($id);
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 404);
        }

        $player->delete();
        return response()->json(['message' => 'Player deleted']);
    }
}
