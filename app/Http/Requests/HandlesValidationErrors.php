<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HandlesValidationErrors
{
    protected function failedValidation(Validator $validator)
    {
        $failedField = array_key_first($validator->failed()) ?? array_key_first($validator->errors()->toArray());
        $value = data_get($this->all(), $failedField);

        if (is_array($value)) {
            $value = '';
        }

        $displayField = $failedField;
        if (str_contains($failedField, '.')) {
            $parts = explode('.', $failedField);
            $displayField = end($parts);
        }

        throw new HttpResponseException(response()->json([
            'message' => "Invalid value for {$displayField}: {$value}"
        ], 400));
    }
}
