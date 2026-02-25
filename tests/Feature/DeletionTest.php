<?php

namespace Tests\Feature;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletionTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=';

    public function test_delete_without_token_fails()
    {
        $player = Player::create(['name' => 'P1', 'position' => 'defender']);

        $res = $this->deleteJson('/api/player/' . $player->id);

        $res->assertStatus(401);
        $res->assertJson(['message' => 'Unauthorized']);
    }

    public function test_delete_with_wrong_token_fails()
    {
        $player = Player::create(['name' => 'P1', 'position' => 'defender']);

        $res = $this->deleteJson('/api/player/' . $player->id, [], [
            'Authorization' => 'Bearer wrong_token'
        ]);

        $res->assertStatus(401);
        $res->assertJson(['message' => 'Unauthorized']);
    }

    public function test_delete_with_correct_token_succeeds()
    {
        $player = Player::create(['name' => 'P1', 'position' => 'defender']);

        $res = $this->deleteJson('/api/player/' . $player->id, [], [
            'Authorization' => 'Bearer ' . self::TOKEN
        ]);

        $res->assertStatus(200);
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
    }

    public function test_delete_non_existent_player_fails()
    {
        $res = $this->deleteJson('/api/player/999', [], [
            'Authorization' => 'Bearer ' . self::TOKEN
        ]);

        $res->assertStatus(404);
        $res->assertJson(['message' => 'Player not found']);
    }
}
