<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function process(Request $request)
    {
        $input = $request->all();

        // If it's a single object, wrap it in an array
        if (isset($input['position'])) {
            $dataToValidate = [$input];
            $isSingle = true;
        } else {
            $dataToValidate = $input;
            $isSingle = false;
        }

        $request->replace($dataToValidate);

        $request->validate([
            '*.position' => 'required|string|in:defender,midfielder,forward',
            '*.mainSkill' => 'required|string|in:defense,attack,speed,strength,stamina',
            '*.numberOfPlayers' => 'required|integer|min:1',
        ]);

        $input = $request->all();

        $selectedPlayers = [];
        $usedPlayerIds = [];

        foreach ($input as $req) {
            $position = $req['position'];
            $mainSkill = $req['mainSkill'];
            $needed = $req['numberOfPlayers'];

            $candidates = Player::where('position', $position)
                ->whereNotIn('id', $usedPlayerIds)
                ->get();

            if ($candidates->count() < $needed) {
                return response()->json(['message' => "Insufficient number of players for position: {$position}"], 400);
            }

            // Sort candidates
            $sorted = $candidates->sort(function ($a, $b) use ($mainSkill) {
                $aSkill = $a->skills->first(fn($s) => $s->skill->value === $mainSkill);
                $bSkill = $b->skills->first(fn($s) => $s->skill->value === $mainSkill);

                $aVal = $aSkill ? $aSkill->value : 0;
                $bVal = $bSkill ? $bSkill->value : 0;

                if ($aVal != $bVal) {
                    return $bVal <=> $aVal;
                }

                $aMaxOther = $a->skills->filter(fn($s) => $s->skill->value !== $mainSkill)->max('value') ?? 0;
                $bMaxOther = $b->skills->filter(fn($s) => $s->skill->value !== $mainSkill)->max('value') ?? 0;

                if ($aMaxOther != $bMaxOther) {
                    return $bMaxOther <=> $aMaxOther;
                }

                return $a->id <=> $b->id;
            });

            $picked = $sorted->take($needed);
            foreach ($picked as $p) {
                $selectedPlayers[] = $p;
                $usedPlayerIds[] = $p->id;
            }
        }

        return response()->json($selectedPlayers);
    }
}
