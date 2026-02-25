<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Support\Facades\DB;

class PlayerService
{
    public function create(array $data): Player
    {
        return DB::transaction(function () use ($data) {
            $player = Player::create($data);

            if (isset($data['playerSkills'])) {
                $player->skills()->createMany($data['playerSkills']);
            }

            return $player;
        });
    }

    public function update(Player $player, array $data): Player
    {
        return DB::transaction(function () use ($player, $data) {
            $player->update($data);

            if (isset($data['playerSkills'])) {
                $newSkills = collect($data['playerSkills']);
                
                // Remove skills not present in the new data
                $player->skills()
                    ->whereNotIn('skill', $newSkills->pluck('skill'))
                    ->delete();

                // Update or create skills
                foreach ($newSkills as $skillData) {
                    $player->skills()->updateOrCreate(
                        ['skill' => $skillData['skill']],
                        ['value' => $skillData['value']]
                    );
                }

                $player->unsetRelation('skills');
            }

            return $player;
        });
    }

    public function delete(Player $player): void
    {
        DB::transaction(fn () => $player->delete());
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Player::with('skills')->get();
    }
}
