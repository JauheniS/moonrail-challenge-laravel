<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            $input = [$input];
        }

        if (is_array($input)) {
            $merged = [];
            foreach ($input as $req) {
                if (!is_array($req)) {
                    continue;
                }
                $position = $req['position'] ?? null;
                $skill = $req['mainSkill'] ?? null;
                $countRaw = $req['numberOfPlayers'] ?? null;

                if ($position !== null && $skill !== null) {
                    $key = $position . ':' . $skill;
                    if (!isset($merged[$key])) {
                        $merged[$key] = $req;
                    } else {
                        $existing = $merged[$key]['numberOfPlayers'] ?? null;
                        $isIntish = static fn($v) => is_int($v) || (is_string($v) && ctype_digit($v));
                        if ($isIntish($existing) && $isIntish($countRaw)) {
                            $merged[$key]['numberOfPlayers'] = (int)$existing + (int)$countRaw;
                        }
                    }
                } else {
                    $merged[uniqid('raw_', true)] = $req;
                }
            }
            $this->replace(array_values($merged));
        } else {
            $this->replace($input);
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

}
