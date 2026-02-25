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
            "message" => "Invalid value for playerSkills.0.skill: defense1"
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

    public function test_error_message_for_field_inside_array()
    {
        $data = [
            "name" => "Test Player",
            "position" => "defender",
            "playerSkills" => [
                [
                    "skill" => "speed",
                    "value" => "not-an-integer"
                ]
            ]
        ];

        $response = $this->postJson('/api/player', $data);

        $response->assertStatus(400);
        $response->assertJson([
            "message" => "Invalid value for playerSkills.0.value: not-an-integer"
        ]);
    }

    public function test_only_first_error_is_returned()
    {
        $data = [
            "name" => "Test Player",
            "position" => "invalid-position",
            "playerSkills" => [
                [
                    "skill" => "invalid-skill",
                    "value" => "not-an-integer"
                ]
            ]
        ];

        $response = $this->postJson('/api/player', $data);

        $response->assertStatus(400);
        $json = $response->json();

        $this->assertArrayHasKey('message', $json);
        $this->assertCount(1, $json);

        $possibleMessages = [
            "Invalid value for position: invalid-position",
            "Invalid value for playerSkills.0.skill: invalid-skill",
            "Invalid value for playerSkills.0.value: not-an-integer"
        ];

        $this->assertContains($json['message'], $possibleMessages);
    }
}
