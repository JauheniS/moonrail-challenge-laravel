<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Support\Collection;
use App\Enums\PlayerSkill;

class TeamSelectionService
{
    /**
     * @param array $requirements
     * @return Collection
     */
    public function select(array $requirements): Collection
    {
        $positionNeeded = $this->aggregateRequirements($requirements);
        $candidateIds = $this->fetchCandidateIds($requirements, $positionNeeded);

        $allCandidates = Player::with('skills')
            ->whereIn('id', array_unique($candidateIds))
            ->get();

        $this->verifySufficiency($positionNeeded, $allCandidates);

        return $this->processSelection($requirements, $allCandidates);
    }

    private function aggregateRequirements(array $requirements): array
    {
        $positionNeeded = [];
        foreach ($requirements as $req) {
            $positionNeeded[$req['position']] = ($positionNeeded[$req['position']] ?? 0) + $req['numberOfPlayers'];
        }
        return $positionNeeded;
    }

    private function fetchCandidateIds(array $requirements, array $positionNeeded): array
    {
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

        $byPosition = [];
        foreach ($requirements as $req) {
            $byPosition[$req['position']][] = $req;
        }

        foreach ($byPosition as $position => $reqs) {
            $neededTotal = $positionNeeded[$position];
            $skills = array_unique(array_column($reqs, 'mainSkill'));

            foreach ($skills as $mainSkill) {
                $topBySkill = Player::select('players.id')
                    ->where('position', $position)
                    ->join('player_skills', 'players.id', '=', 'player_skills.player_id')
                    ->where('player_skills.skill', $mainSkill)
                    ->orderBy('player_skills.value', 'DESC')
                    ->orderBy('players.id', 'ASC')
                    ->limit($neededTotal)
                    ->pluck('id')
                    ->toArray();

                array_push($candidateIds, ...$topBySkill);
            }
        }

        return $candidateIds;
    }

    private function verifySufficiency(array $positionNeeded, Collection $allCandidates): void
    {
        foreach ($positionNeeded as $position => $needed) {
            $available = $allCandidates->filter(
                fn($p) => $p->position->value === $position,
            )->count();

            if ($available < $needed) {
                throw new \RuntimeException("Insufficient number of players for position: {$position}");
            }
        }
    }

    private function processSelection(array $requirements, Collection $allCandidates): Collection
    {
        $flatReqs = [];
        foreach ($requirements as $i => $req) {
            for ($j = 0; $j < $req['numberOfPlayers']; $j++) {
                $flatReqs[] = [
                    'id' => "{$i}_{$j}",
                    'position' => $req['position'],
                    'mainSkill' => $req['mainSkill'],
                    'original_index' => $i,
                ];
            }
        }

        $assignments = [];
        $usedPlayerIds = [];
        $assignedReqIds = [];

        $pairs = [];
        foreach ($flatReqs as $req) {
            foreach ($allCandidates as $player) {
                if ($player->position->value !== $req['position']) {
                    continue;
                }

                $skillModel = $player->skills->first(fn($s) => $s->skill->value === $req['mainSkill']);
                $hasSkill = $skillModel !== null;
                $skillValue = $hasSkill ? $skillModel->value : 0;
                $maxSkillValue = $player->skills->max('value') ?? 0;

                $pairs[] = [
                    'player' => $player,
                    'req_id' => $req['id'],
                    'has_skill' => $hasSkill,
                    'skill_value' => $skillValue,
                    'max_skill' => $maxSkillValue,
                    'player_id' => $player->id,
                    'original_index' => $req['original_index'],
                ];
            }
        }

        usort($pairs, function ($a, $b) {
            if ($a['has_skill'] !== $b['has_skill']) {
                return $b['has_skill'] <=> $a['has_skill'];
            }
            if ($a['has_skill'] && $a['skill_value'] !== $b['skill_value']) {
                return $b['skill_value'] <=> $a['skill_value'];
            }
            if ($a['max_skill'] !== $b['max_skill']) {
                return $b['max_skill'] <=> $a['max_skill'];
            }
            return $a['player_id'] <=> $b['player_id'];
        });

        foreach ($pairs as $pair) {
            if (!in_array($pair['player_id'], $usedPlayerIds) && !in_array($pair['req_id'], $assignedReqIds)) {
                $assignments[$pair['req_id']] = $pair['player'];
                $usedPlayerIds[] = $pair['player_id'];
                $assignedReqIds[] = $pair['req_id'];
            }
        }

        $selectedPlayers = collect();
        foreach ($flatReqs as $req) {
            if (isset($assignments[$req['id']])) {
                $selectedPlayers->push($assignments[$req['id']]);
            }
        }

        return $selectedPlayers;
    }
}
