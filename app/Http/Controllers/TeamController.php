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

        $positionNeeded = [];
        foreach ($input as $req) {
            $positionNeeded[$req['position']] = ($positionNeeded[$req['position']] ?? 0) + $req['numberOfPlayers'];
        }

        $candidateIds = [];
        foreach ($positionNeeded as $position => $needed) {
            $topByMaxSkill = Player::select('players.id')
                ->where('position', $position)
                ->withMax('skills', 'value')
                ->orderByRaw('COALESCE(skills_max_value, 0) DESC')
                ->orderBy('players.id', 'ASC')
                ->limit($needed)
                ->pluck('id')
                ->toArray();

            array_push($candidateIds, ...$topByMaxSkill);
        }

        foreach ($input as $req) {
            $position = $req['position'];
            $mainSkill = $req['mainSkill'];
            $needed = $positionNeeded[$position];

            $topBySkill = Player::select('players.id')
                ->where('position', $position)
                ->join('player_skills', 'players.id', '=', 'player_skills.player_id')
                ->where('player_skills.skill', $mainSkill)
                ->orderBy('player_skills.value', 'DESC')
                ->orderBy('players.id', 'ASC')
                ->limit($needed)
                ->pluck('id')
                ->toArray();

            array_push($candidateIds, ...$topBySkill);
        }

        $allCandidates = Player::with('skills')
            ->whereIn('id', array_unique($candidateIds))
            ->get();

        foreach ($positionNeeded as $position => $needed) {
            $available = $allCandidates->filter(
                fn($p) => $p->position->value === $position,
            )->count();
            if ($available < $needed) {
                return response()->json([
                    'message' => "Insufficient number of players for position: {$position}",
                ], 400);
            }
        }

        $selectedPlayers = [];
        $usedPlayerIds = [];

        foreach ($input as $req) {
            $position = $req['position'];
            $mainSkill = $req['mainSkill'];
            $needed = $req['numberOfPlayers'];

            $candidates = $allCandidates
                ->filter(fn($p) => $p->position->value === $position)
                ->whereNotIn('id', $usedPlayerIds);

            $sorted = $candidates->sort(function ($a, $b) use ($mainSkill) {
                $aSkillModel = $a->skills->first(fn($s) => $s->skill->value === $mainSkill);
                $bSkillModel = $b->skills->first(fn($s) => $s->skill->value === $mainSkill);
                $aHas = $aSkillModel !== null;
                $bHas = $bSkillModel !== null;
                if ($aHas !== $bHas) {
                    return $bHas <=> $aHas;
                }
                if ($aHas && $aSkillModel->value !== $bSkillModel->value) {
                    return $bSkillModel->value <=> $aSkillModel->value;
                }
                $aMax = $a->skills->max('value') ?? 0;
                $bMax = $b->skills->max('value') ?? 0;
                if ($aMax !== $bMax) {
                    return $bMax <=> $aMax;
                }
                return $a->id <=> $b->id;
            })->values();

            $picked = $sorted->take($needed);
            foreach ($picked as $p) {
                $selectedPlayers[] = $p;
                $usedPlayerIds[] = $p->id;
            }
        }

        return PlayerResource::collection($selectedPlayers);
    }
}
