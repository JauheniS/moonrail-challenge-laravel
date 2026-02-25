<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HandlesValidationErrors
{
    protected function failedValidation(Validator $validator)
    {
        $failed = $validator->failed();
        $failedField = array_key_first($failed) ?? array_key_first($validator->errors()->toArray());

        if (!$failedField) {
            return;
        }

        $value = data_get($this->all(), $failedField);
        if (is_array($value)) {
            $value = '';
        }

        $rules = $failed[$failedField] ?? [];
        if (isset($rules['Distinct']) && str_contains($failedField, 'skill')) {
            throw new HttpResponseException(response()->json([
                'message' => "Invalid value for {$failedField}: duplicate"
            ], 400));
        }

        $displayField = $failedField;
        if (str_contains($failedField, '.') && !str_starts_with($failedField, 'playerSkills.')) {
            $parts = explode('.', $failedField);
            $displayField = end($parts);
        }

        throw new HttpResponseException(response()->json([
            'message' => "Invalid value for {$displayField}: {$value}"
        ], 400));
    }
}
