<?php

namespace Tests\Unit;

use App\Models\Player;
use App\Models\PlayerSkill;
use App\Services\PlayerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PlayerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PlayerService();
    }

    public function test_it_can_create_a_player_with_skills()
    {
        $data = [
            'name' => 'John Doe',
            'position' => 'midfielder',
            'playerSkills' => [
                ['skill' => 'speed', 'value' => 80],
                ['skill' => 'strength', 'value' => 70],
            ],
        ];

        $player = $this->service->create($data);

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals('John Doe', $player->name);
        $this->assertCount(2, $player->skills);
        $this->assertDatabaseHas('players', ['name' => 'John Doe']);
        $this->assertDatabaseHas('player_skills', ['player_id' => $player->id, 'skill' => 'speed', 'value' => 80]);
    }

    public function test_it_can_update_a_player_and_sync_skills()
    {
        $player = Player::factory()->create(['name' => 'Old Name']);
        $player->skills()->create(['skill' => 'speed', 'value' => 50]);
        $player->skills()->create(['skill' => 'stamina', 'value' => 40]);

        $updateData = [
            'name' => 'New Name',
            'playerSkills' => [
                ['skill' => 'speed', 'value' => 90], // Update existing
                ['skill' => 'strength', 'value' => 60], // Add new
            ],
        ];

        $updatedPlayer = $this->service->update($player, $updateData);

        $this->assertEquals('New Name', $updatedPlayer->name);
        $this->assertCount(2, $updatedPlayer->skills);
        
        // speed should be updated
        $this->assertDatabaseHas('player_skills', ['player_id' => $player->id, 'skill' => 'speed', 'value' => 90]);
        // strength should be added
        $this->assertDatabaseHas('player_skills', ['player_id' => $player->id, 'skill' => 'strength', 'value' => 60]);
        // stamina should be deleted
        $this->assertDatabaseMissing('player_skills', ['player_id' => $player->id, 'skill' => 'stamina']);
    }

    public function test_it_can_delete_a_player()
    {
        $player = Player::factory()->create();
        $player->skills()->create(['skill' => 'speed', 'value' => 50]);

        $this->service->delete($player);

        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        $this->assertDatabaseMissing('player_skills', ['player_id' => $player->id]);
    }

    public function test_it_can_get_all_players()
    {
        Player::factory()->count(3)->create();
        
        $players = $this->service->getAll();
        
        $this->assertCount(3, $players);
        $this->assertTrue($players->first()->relationLoaded('skills'));
    }
}
