<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SelectionLogicTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_selection_logic_with_tie_breaks()
    {
        $p1 = Player::create(['name' => 'P1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'speed', 'value' => 80]);
        $p1->skills()->create(['skill' => 'defense', 'value' => 60]);

        $p2 = Player::create(['name' => 'P2', 'position' => 'defender']);
        $p2->skills()->create(['skill' => 'speed', 'value' => 80]);
        $p2->skills()->create(['skill' => 'defense', 'value' => 90]);

        $p3 = Player::create(['name' => 'P3', 'position' => 'defender']);
        $p3->skills()->create(['skill' => 'speed', 'value' => 70]);

        $requirements = [
            'position' => "defender",
            'mainSkill' => "speed",
            'numberOfPlayers' => 2
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $res->assertStatus(200);
        $data = $res->json();

        $this->assertCount(2, $data);
        $this->assertEquals('P2', $data[0]['name']);
        $this->assertEquals('P1', $data[1]['name']);
    }

    public function test_selection_logic_multiple_requirements()
    {
        $p1 = Player::create(['name' => 'P1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'speed', 'value' => 80]);

        $p2 = Player::create(['name' => 'P2', 'position' => 'midfielder']);
        $p2->skills()->create(['skill' => 'attack', 'value' => 90]);

        $requirements = [
            [
                'position' => "defender",
                'mainSkill' => "speed",
                'numberOfPlayers' => 1
            ],
            [
                'position' => "midfielder",
                'mainSkill' => "attack",
                'numberOfPlayers' => 1
            ]
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $res->assertStatus(200);
        $data = $res->json();

        $this->assertCount(2, $data);
        $this->assertEquals('P1', $data[0]['name']);
        $this->assertEquals('P2', $data[1]['name']);
    }

    public function test_insufficient_players()
    {
        Player::create(['name' => 'P1', 'position' => 'defender'])
            ->skills()->create(['skill' => 'speed', 'value' => 80]);

        $requirements = [
            'position' => "defender",
            'mainSkill' => "speed",
            'numberOfPlayers' => 2
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $res->assertStatus(400);
        $res->assertJson(['message' => "Insufficient number of players for position: defender"]);
    }
}
