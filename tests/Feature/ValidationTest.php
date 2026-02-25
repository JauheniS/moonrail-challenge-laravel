<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_creation_invalid_position()
    {
        $data = [
            "name" => "player name",
            "position" => "midfielder1",
            "playerSkills" => [
                [
                    "skill" => "defense",
                    "value" => 60
                ]
            ]
        ];

        $res = $this->postJson('/api/player', $data);

        $res->assertStatus(400);
        $res->assertJson([
            "message" => "Invalid value for position: midfielder1"
        ]);
    }

    public function test_player_creation_invalid_skill()
    {
        $data = [
            "name" => "player name",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "defense1",
                    "value" => 60
                ]
            ]
        ];

        $res = $this->postJson('/api/player', $data);

        $res->assertStatus(400);
        $res->assertJson([
            "message" => "Invalid value for skill: defense1"
        ]);
    }

    public function test_player_creation_missing_skills()
    {
        $data = [
            "name" => "player name",
            "position" => "midfielder",
            "playerSkills" => []
        ];

        $res = $this->postJson('/api/player', $data);

        $res->assertStatus(400);
        // Message will be "Invalid value for playerSkills: " because we set $value = '' for arrays
        $res->assertJsonFragment(["message" => "Invalid value for playerSkills: "]);
    }

    public function test_team_process_invalid_position()
    {
        $data = [
            "position" => "midfielder1",
            "mainSkill" => "speed",
            "numberOfPlayers" => 1
        ];

        $res = $this->postJson('/api/team/process', $data);

        $res->assertStatus(400);
        $res->assertJson([
            "message" => "Invalid value for position: midfielder1"
        ]);
    }

    public function test_team_process_invalid_numberOfPlayers()
    {
        $data = [
            [
                "position" => "midfielder",
                "mainSkill" => "speed",
                "numberOfPlayers" => "abc"
            ]
        ];

        $res = $this->postJson('/api/team/process', $data);

        $res->assertStatus(400);
        $res->assertJson([
            "message" => "Invalid value for numberOfPlayers: abc"
        ]);
    }
}
