<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamSelectionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_process_duplicate_position_skill_combo_fails()
    {
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

        $res->assertStatus(400);
        $res->assertJson([
            "message" => "Invalid value for mainSkill: speed"
        ]);
    }
}
