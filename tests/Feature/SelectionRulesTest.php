<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SelectionRulesTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_selection_when_no_player_has_requested_skill()
    {
        $p1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'speed', 'value' => 90]);

        $p2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $p2->skills()->create(['skill' => 'strength', 'value' => 20]);

        $p3 = Player::create(['name' => 'Player 3', 'position' => 'defender']);
        $p3->skills()->create(['skill' => 'stamina', 'value' => 95]);

        $requirements = [
            'position' => "defender",
            'mainSkill' => "defense",
            'numberOfPlayers' => 1
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $res->assertStatus(200);
        $data = $res->json();

        $this->assertCount(1, $data);
        $this->assertEquals('Player 3', $data[0]['name']);
    }

    public function test_selection_highest_skill_multiple_skills()
    {
        $p1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'stamina', 'value' => 90]);
        $p1->skills()->create(['skill' => 'speed', 'value' => 100]);
        $p1->skills()->create(['skill' => 'strength', 'value' => 20]);

        $p2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $p2->skills()->create(['skill' => 'stamina', 'value' => 80]);
        $p2->skills()->create(['skill' => 'speed', 'value' => 80]);
        $p2->skills()->create(['skill' => 'strength', 'value' => 80]);

        $p3 = Player::create(['name' => 'Player 3', 'position' => 'defender']);
        $p3->skills()->create(['skill' => 'stamina', 'value' => 95]);
        $p3->skills()->create(['skill' => 'speed', 'value' => 90]);
        $p3->skills()->create(['skill' => 'strength', 'value' => 50]);

        $requirements = [
            'position' => "defender",
            'mainSkill' => "defense",
            'numberOfPlayers' => 1
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $res->assertStatus(200);
        $data = $res->json();

        $this->assertCount(1, $data);
        $this->assertEquals('Player 1', $data[0]['name']);
    }

    public function test_select_best_2_defenders_when_no_defense_skill_exists()
    {
        $p1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'stamina', 'value' => 100]);

        $p2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $p2->skills()->create(['skill' => 'speed', 'value' => 90]);

        $p3 = Player::create(['name' => 'Player 3', 'position' => 'defender']);
        $p3->skills()->create(['skill' => 'strength', 'value' => 80]);

        $requirements = [
            'position' => "defender",
            'mainSkill' => "defense",
            'numberOfPlayers' => 2
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $res->assertStatus(200);
        $data = $res->json();

        $this->assertCount(2, $data);
        $this->assertEquals('Player 1', $data[0]['name']);
        $this->assertEquals('Player 2', $data[1]['name']);
    }
}
