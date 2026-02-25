<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BulkPlayerCreationTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_cannot_create_player_with_array_of_one_player()
    {
        $data = [
            [
                "name" => "Player 1",
                "position" => "defender",
                "playerSkills" => [
                    ["skill" => "defense", "value" => 80]
                ]
            ]
        ];

        $res = $this->postJson(self::REQ_URI, $data);

        $res->assertStatus(400);
        $this->assertEquals(0, Player::count());
    }

    public function test_cannot_create_multiple_players_at_once()
    {
        $data = [
            [
                "name" => "Player 1",
                "position" => "defender",
                "playerSkills" => [
                    ["skill" => "defense", "value" => 80]
                ]
            ],
            [
                "name" => "Player 2",
                "position" => "midfielder",
                "playerSkills" => [
                    ["skill" => "attack", "value" => 90]
                ]
            ]
        ];

        $res = $this->postJson(self::REQ_URI, $data);

        $res->assertStatus(400);
        $this->assertEquals(0, Player::count());
    }

    public function test_cannot_update_with_multiple_players()
    {
        $player = Player::create(['name' => 'Original', 'position' => 'defender']);

        $data = [
            [
                "name" => "Updated 1",
                "position" => "midfielder",
                "playerSkills" => [
                    ["skill" => "attack", "value" => 90]
                ]
            ],
            [
                "name" => "Updated 2",
                "position" => "forward",
                "playerSkills" => [
                    ["skill" => "speed", "value" => 80]
                ]
            ]
        ];

        $res = $this->putJson(self::REQ_URI . $player->id, $data);

        $res->assertStatus(400);
        $player->refresh();
        $this->assertEquals('Original', $player->name);
    }
}
