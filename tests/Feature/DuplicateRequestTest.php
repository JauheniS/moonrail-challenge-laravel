<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DuplicateRequestTest extends PlayerControllerBaseTest
{
    use RefreshDatabase;

    public function test_duplicate_position_and_skill_are_merged()
    {
        Player::create(['name' => 'P1', 'position' => 'defender'])
            ->skills()->create(['skill' => 'speed', 'value' => 80]);
        Player::create(['name' => 'P2', 'position' => 'defender'])
            ->skills()->create(['skill' => 'speed', 'value' => 70]);
        Player::create(['name' => 'P3', 'position' => 'defender'])
            ->skills()->create(['skill' => 'speed', 'value' => 60]);

        $requirements = [
            [
                'position' => 'defender',
                'mainSkill' => 'speed',
                'numberOfPlayers' => 1
            ],
            [
                'position' => 'defender',
                'mainSkill' => 'speed',
                'numberOfPlayers' => 1
            ]
        ];

        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $res->assertStatus(200);
        $this->assertCount(2, $res->json());
    }
}
