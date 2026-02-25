<?php

namespace Tests\Feature;

class PlayerControllerDeleteTest extends PlayerControllerBaseTest
{

    public function test_sample()
    {
        $res = $this->delete(self::REQ_URI . '1');

        $this->assertNotNull($res);
    }
}
