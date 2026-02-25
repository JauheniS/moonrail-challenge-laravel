<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\PlayerSkill as PlayerSkillModel;
use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SelectionRulesTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    /**
     * Rule #4: If there are no players in the database with the desired skill,
     * the app should find the highest skill value for any players in the selected position.
     */
    public function test_selection_when_no_player_has_requested_skill()
    {
        // player 1 has {speed: 90}
        $p1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'speed', 'value' => 90]);

        // player 2 has {strength: 20}
        $p2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $p2->skills()->create(['skill' => 'strength', 'value' => 20]);

        // player 3 has {stamina: 95}
        $p3 = Player::create(['name' => 'Player 3', 'position' => 'defender']);
        $p3->skills()->create(['skill' => 'stamina', 'value' => 95]);

        // Requirements ask for a defender with defense skill.
        // Rule #4: Select player 3 because it has the highest skill value (stamina 95)
        // among all defenders, even though no defender has defense skill.
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

    /**
     * Rule #4: The same rule should be applied if the player has multiple skills.
     */
    public function test_selection_highest_skill_multiple_skills()
    {
        // player 1 has {stamina: 90, speed: 100, strength: 20}
        $p1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'stamina', 'value' => 90]);
        $p1->skills()->create(['skill' => 'speed', 'value' => 100]);
        $p1->skills()->create(['skill' => 'strength', 'value' => 20]);

        // player 2 has {stamina: 80, speed: 80, strength: 80}
        $p2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $p2->skills()->create(['skill' => 'stamina', 'value' => 80]);
        $p2->skills()->create(['skill' => 'speed', 'value' => 80]);
        $p2->skills()->create(['skill' => 'strength', 'value' => 80]);

        // player 3 has {stamina: 95, speed 90, strength: 50}
        $p3 = Player::create(['name' => 'Player 3', 'position' => 'defender']);
        $p3->skills()->create(['skill' => 'stamina', 'value' => 95]);
        $p3->skills()->create(['skill' => 'speed', 'value' => 90]);
        $p3->skills()->create(['skill' => 'strength', 'value' => 50]);

        // Requirements specify a defender with defense skill.
        // App should select player 1, because it has highest skill: speed 100.
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

    /**
     * Test picking 2 defenders. Rule: find best 2 defenders with desired skill
     * and use rule number 4 if no available defenders with desired skill.
     */
    public function test_select_best_2_defenders_when_no_defense_skill_exists()
    {
        // player 1: stamina 100
        $p1 = Player::create(['name' => 'Player 1', 'position' => 'defender']);
        $p1->skills()->create(['skill' => 'stamina', 'value' => 100]);

        // player 2: speed 90
        $p2 = Player::create(['name' => 'Player 2', 'position' => 'defender']);
        $p2->skills()->create(['skill' => 'speed', 'value' => 90]);

        // player 3: strength 80
        $p3 = Player::create(['name' => 'Player 3', 'position' => 'defender']);
        $p3->skills()->create(['skill' => 'strength', 'value' => 80]);

        // Request 2 defenders with defense skill.
        // Expected: Player 1 (stamina 100) and Player 2 (speed 90).
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
