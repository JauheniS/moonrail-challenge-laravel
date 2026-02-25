<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rules\Enum;
use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;

class TeamProcessRequest extends FormRequest
{
    use HandlesValidationErrors;

    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $input = $this->all();
        if (isset($input['position'])) {
            $this->replace([$input]);
        }
    }

    public function rules()
    {
        return [
            '*.position' => ['required', 'string', new Enum(PlayerPosition::class)],
            '*.mainSkill' => ['required', 'string', new Enum(PlayerSkill::class)],
            '*.numberOfPlayers' => 'required|integer|min:1',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $input = $this->all();
            if (!is_array($input)) {
                return;
            }

            $seen = [];
            foreach ($input as $index => $req) {
                if (!is_array($req) || !isset($req['position']) || !isset($req['mainSkill'])) {
                    continue;
                }

                $combo = $req['position'] . ':' . $req['mainSkill'];
                if (in_array($combo, $seen)) {
                    $validator->errors()->add("{$index}.mainSkill", "Invalid");
                    break;
                }
                $seen[] = $combo;
            }
        });
    }
}
