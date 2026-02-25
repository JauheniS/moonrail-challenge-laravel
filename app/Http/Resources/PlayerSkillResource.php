<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerSkillResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'skill' => $this->skill,
            'value' => $this->value,
            'playerId' => $this->player_id,
        ];
    }
}
