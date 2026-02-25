<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;

class UpdatePlayerRequest extends FormRequest
{
    use HandlesValidationErrors;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'position' => ['required', new Enum(PlayerPosition::class)],
            'playerSkills' => 'required|array|min:1',
            'playerSkills.*.skill' => ['required', new Enum(PlayerSkill::class), 'distinct'],
            'playerSkills.*.value' => 'required|integer',
        ];
    }
}
