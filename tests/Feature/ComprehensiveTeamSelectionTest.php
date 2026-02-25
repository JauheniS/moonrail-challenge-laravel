<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComprehensiveTeamSelectionTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_merging_and_multi_position_selection()
    {
        Player::create(['name' => 'D1', 'position' => 'defender'])->skills()->create(['skill' => 'speed', 'value' => 95]);
        Player::create(['name' => 'D2', 'position' => 'defender'])->skills()->create(['skill' => 'speed', 'value' => 85]);
        Player::create(['name' => 'D3', 'position' => 'defender'])->skills()->create(['skill' => 'strength', 'value' => 70]);

        Player::create(['name' => 'M1', 'position' => 'midfielder'])->skills()->create(['skill' => 'speed', 'value' => 90]);
        Player::create(['name' => 'M2', 'position' => 'midfielder'])->skills()->create(['skill' => 'attack', 'value' => 75]);

        $requirements = [
            ['position' => 'defender', 'mainSkill' => 'speed', 'numberOfPlayers' => 1],
            ['position' => 'defender', 'mainSkill' => 'speed', 'numberOfPlayers' => 1],
            ['position' => 'midfielder', 'mainSkill' => 'speed', 'numberOfPlayers' => 1],
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $res->assertStatus(200);
        $data = $res->json();

        $this->assertCount(3, $data);
        $names = array_column($data, 'name');
        $this->assertContains('D1', $names);
        $this->assertContains('D2', $names);
        $this->assertContains('M1', $names);
        $this->assertCount(count(array_unique($names)), $names);
    }

    public function test_error_when_no_players_for_position()
    {
        Player::create(['name' => 'M1', 'position' => 'midfielder'])->skills()->create(['skill' => 'speed', 'value' => 70]);

        $requirements = [
            ['position' => 'defender', 'mainSkill' => 'speed', 'numberOfPlayers' => 1]
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $res->assertStatus(400);
        $res->assertJson(['message' => 'Insufficient number of players for position: defender']);
    }

    public function test_error_when_number_of_players_missing()
    {
        $requirements = [
            ['position' => 'defender', 'mainSkill' => 'speed']
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $res->assertStatus(400);
        $res->assertJsonFragment(['message' => 'Invalid value for numberOfPlayers: ']);
    }

    public function test_ok_when_no_requested_skill_but_enough_players()
    {
        Player::create(['name' => 'D1', 'position' => 'defender'])->skills()->create(['skill' => 'speed', 'value' => 90]);
        Player::create(['name' => 'D2', 'position' => 'defender'])->skills()->create(['skill' => 'stamina', 'value' => 88]);
        Player::create(['name' => 'D3', 'position' => 'defender'])->skills()->create(['skill' => 'strength', 'value' => 70]);

        $requirements = [
            ['position' => 'defender', 'mainSkill' => 'defense', 'numberOfPlayers' => 2]
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);
        $res->assertStatus(200);
        $data = $res->json();
        $this->assertCount(2, $data);
        $names = array_column($data, 'name');
        $this->assertEquals(['D1', 'D2'], $names);
    }
}
