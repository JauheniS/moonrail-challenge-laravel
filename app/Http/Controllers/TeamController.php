<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamProcessRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;

class TeamController extends Controller
{
    public function process(TeamProcessRequest $request)
    {
        $input = $request->validated();

        $positions = array_unique(array_column($input, 'position'));
        $allCandidates = Player::with('skills')->whereIn('position', $positions)->get();

        $selectedPlayers = [];
        $usedPlayerIds = [];

        foreach ($input as $req) {
            $position = $req['position'];
            $mainSkill = $req['mainSkill'];
            $needed = $req['numberOfPlayers'];

            $candidates = $allCandidates->filter(fn ($p) => $p->position->value === $position)
                ->whereNotIn('id', $usedPlayerIds);

            if ($candidates->count() < $needed) {
                return response()->json(['message' => "Insufficient number of players for position: {$position}"], 400);
            }

            $sorted = $candidates->sort(function ($a, $b) use ($mainSkill) {
                $aSkill = $a->skills->first(fn ($s) => $s->skill->value === $mainSkill);
                $bSkill = $b->skills->first(fn ($s) => $s->skill->value === $mainSkill);

                $aVal = $aSkill ? $aSkill->value : 0;
                $bVal = $bSkill ? $bSkill->value : 0;

                if ($aVal != $bVal) {
                    return $bVal <=> $aVal;
                }

                $aMaxOther = $a->skills->filter(fn ($s) => $s->skill->value !== $mainSkill)->max('value') ?? 0;
                $bMaxOther = $b->skills->filter(fn ($s) => $s->skill->value !== $mainSkill)->max('value') ?? 0;

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

        return PlayerResource::collection($selectedPlayers);
    }
}
