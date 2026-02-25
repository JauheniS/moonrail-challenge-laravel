<?php

namespace Database\Factories;

use App\Enums\PlayerPosition;
use App\Models\Player;
use App\Models\PlayerSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'position' => $this->faker->randomElement(PlayerPosition::cases()),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Player $player) {
            if ($player->skills()->count() === 0) {
                PlayerSkill::factory()->create([
                    'player_id' => $player->id,
                ]);
            }
        });
    }
}
