<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ImageOrBase64 implements Rule
{
    public function passes($attribute, $value)
    {
        if (!$value) return true; // nullable

        // Check if it's a URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return true;
        }

        // Check if it's base64 image
        if (preg_match('/^data:image\/(\w+);base64,/', $value)) {
            return true;
        }

        return false;
    }

    public function message()
    {
        return 'The :attribute must be a valid URL or a Base64 encoded image.';
    }
}
