<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_player_returns_correct_structure()
    {
        $data = [
            "name" => "player name 2",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "attack",
                    "value" => 60
                ]
            ]
        ];

        $res = $this->postJson('/api/player', $data);

        $res->assertStatus(201);
        $res->assertJsonStructure([
            "id",
            "name",
            "position",
            "playerSkills" => [
                "*" => [
                    "id",
                    "skill",
                    "value",
                    "playerId"
                ]
            ]
        ]);

        $res->assertJsonFragment([
            "name" => "player name 2",
            "position" => "midfielder",
        ]);

        $skills = $res->json('playerSkills');
        $this->assertCount(1, $skills);
        $this->assertEquals('attack', $skills[0]['skill']);
        $this->assertEquals(60, $skills[0]['value']);
        $this->assertEquals($res->json('id'), $skills[0]['playerId']);
    }

    public function test_update_player_returns_correct_structure()
    {
        $player = Player::create(['name' => 'P1', 'position' => 'defender']);
        $player->skills()->create(['skill' => 'defense', 'value' => 60]);

        $data = [
            "name" => "player name updated",
            "position" => "midfielder",
            "playerSkills" => [
                [
                    "skill" => "strength",
                    "value" => 40
                ]
            ]
        ];

        $res = $this->putJson('/api/player/' . $player->id, $data);

        $res->assertStatus(200);
        $res->assertJsonFragment([
            "name" => "player name updated",
            "position" => "midfielder",
        ]);

        $skills = $res->json('playerSkills');
        $this->assertCount(1, $skills);
        $this->assertEquals('strength', $skills[0]['skill']);
        $this->assertEquals(40, $skills[0]['value']);
        $this->assertEquals($player->id, $skills[0]['playerId']);
    }

    public function test_list_players()
    {
        $p1 = Player::create(['name' => 'P1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'defense', 'value' => 60]);

        $p2 = Player::create(['name' => 'P2', 'position' => 'midfielder']);
        $p2->skills()->create(['skill' => 'attack', 'value' => 70]);

        $res = $this->getJson('/api/player');

        $res->assertStatus(200);
        $res->assertJsonCount(2);
        $res->assertJsonStructure([
            "*" => [
                "id",
                "name",
                "position",
                "playerSkills"
            ]
        ]);
    }
}
