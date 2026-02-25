<?php

namespace Tests\Feature;


class TeamControllerTest extends PlayerControllerBaseTest
{
    public function test_sample()
    {
        $requirements =
            [
                'position' => "defender",
                'mainSkill' => "speed",
                'numberOfPlayers' => 1
            ];


        $res = $this->postJson(self::REQ_TEAM_URI, $requirements);

        $this->assertNotNull($res);
    }
}
