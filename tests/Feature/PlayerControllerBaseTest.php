<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class PlayerControllerBaseTest extends TestCase
{
    use RefreshDatabase;

    final const REQ_URI = '/api/player/';
    final const REQ_TEAM_URI = '/api/team/process';
 

    protected function log($data){
        fwrite(STDERR, print_r($data, TRUE));
    }
}
