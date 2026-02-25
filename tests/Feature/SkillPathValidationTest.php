<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillPathValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_creation_invalid_skill_value_shows_full_path()
    {
        $data = [
            "name" => "player name",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "defense",
                    "value" => "not-an-integer"
                ]
            ]
        ];

        $res = $this->postJson('/api/player', $data);

        $res->assertStatus(400);
        $res->assertJson([
            "message" => "Invalid value for playerSkills.0.value: not-an-integer"
        ]);
    }

    public function test_player_creation_invalid_skill_name_shows_full_path()
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

    public function test_player_update_invalid_skill_value_shows_full_path()
    {
        $player = \App\Models\Player::create(['name' => 'P1', 'position' => 'defender']);

        $data = [
            "name" => "player name updated",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "strength",
                    "value" => "invalid-val"
                ]
            ]
        ];

        $res = $this->putJson('/api/player/' . $player->id, $data);

        $res->assertStatus(400);
        $res->assertJson([
            "message" => "Invalid value for playerSkills.0.value: invalid-val"
        ]);
    }
}
