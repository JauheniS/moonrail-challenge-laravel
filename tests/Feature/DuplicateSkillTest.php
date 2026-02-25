<?php

namespace Tests\Feature;

class DuplicateSkillTest extends PlayerControllerBaseTest
{
    public function test_player_creation_fails_with_duplicate_skills()
    {
        $data = [
            "name" => "player name updated",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "strength",
                    "value" => 40
                ],
                [
                    "skill" => "speed",
                    "value" => 60
                ],
                [
                    "skill" => "strength",
                    "value" => 60
                ],
                [
                    "skill" => "stamina",
                    "value" => 30
                ],
                [
                    "skill" => "stamina",
                    "value" => 56
                ],
                [
                    "skill" => "stamina",
                    "value" => 45
                ]
            ]
        ];

        $response = $this->postJson(self::REQ_URI, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => "Invalid value for playerSkills.0.skill: duplicate"
        ]);
    }

    public function test_player_creation_still_returns_standard_error_for_invalid_skill()
    {
        $data = [
            "name" => "player name",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "invalid_skill",
                    "value" => 60
                ]
            ]
        ];

        $response = $this->postJson(self::REQ_URI, $data);

        $response->assertStatus(400);
        $response->assertJson([
            "message" => "Invalid value for playerSkills.0.skill: invalid_skill"
        ]);
    }

    public function test_player_update_fails_with_duplicate_skills()
    {
        $player = \App\Models\Player::factory()->create();

        $data = [
            "name" => "player name updated",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "speed",
                    "value" => 34
                ],
                [
                    "skill" => "defense",
                    "value" => 90
                ],
                [
                    "skill" => "strength",
                    "value" => 40
                ],
                [
                    "skill" => "strength",
                    "value" => 60
                ]
            ]
        ];

        $response = $this->putJson(self::REQ_URI . $player->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => "Invalid value for playerSkills.2.skill: duplicate"
        ]);
    }
}
