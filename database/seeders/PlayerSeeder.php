<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\PlayerSkill;
use Illuminate\Database\Seeder;
use App\Enums\PlayerSkill as PlayerSkillEnum;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allSkills = PlayerSkillEnum::cases();

        $totalPlayers = 1000;
        $chunkSize = 500;

        for ($i = 0; $i < $totalPlayers; $i += $chunkSize) {
            Player::factory()->count($chunkSize)->create()->each(function ($player) use ($allSkills) {
                $additionalCount = random_int(0, 2);

                if ($additionalCount > 0) {
                    $existingSkill = $player->skills->first()->skill;

                    $availableSkills = array_filter(
                        $allSkills,
                        static fn($s) => $s !== $existingSkill
                    );

                    if (!empty($availableSkills)) {
                        $skillsToPick = collect($availableSkills)->random(min($additionalCount, count($availableSkills)));

                        foreach ($skillsToPick as $skill) {
                            PlayerSkill::factory()->create([
                                'player_id' => $player->id,
                                'skill' => $skill,
                            ]);
                        }
                    }
                }
            });
        }
    }
}
