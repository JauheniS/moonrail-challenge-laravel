<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamSelectionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_process_duplicate_position_skill_combo_is_merged()
    {
        Player::create(['name' => 'P1', 'position' => 'defender'])
            ->skills()->create(['skill' => 'speed', 'value' => 90]);
        Player::create(['name' => 'P2', 'position' => 'defender'])
            ->skills()->create(['skill' => 'speed', 'value' => 80]);

        $data = [
            [
                "position" => "defender",
                "mainSkill" => "speed",
                "numberOfPlayers" => 1
            ],
            [
                "position" => "defender",
                "mainSkill" => "speed",
                "numberOfPlayers" => 1
            ]
        ];

        $res = $this->postJson('/api/team/process', $data);

        $res->assertStatus(200);
        $this->assertCount(2, $res->json());
    }
}
