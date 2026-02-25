<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    use HandlesValidationErrors;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'position' => 'required|in:defender,midfielder,forward',
            'playerSkills' => 'required|array|min:1',
            'playerSkills.*.skill' => 'required|in:defense,attack,speed,strength,stamina',
            'playerSkills.*.value' => 'required|integer',
        ];
    }
}
