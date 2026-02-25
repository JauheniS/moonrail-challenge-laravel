<?php

namespace Database\Factories;

use App\Enums\PlayerSkill as PlayerSkillEnum;
use App\Models\Player;
use App\Models\PlayerSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerSkillFactory extends Factory
{
    protected $model = PlayerSkill::class;

    public function definition()
    {
        return [
            'skill' => $this->faker->randomElement(PlayerSkillEnum::cases()),
            'value' => $this->faker->numberBetween(0, 100),
            'player_id' => Player::factory(),
        ];
    }
}
