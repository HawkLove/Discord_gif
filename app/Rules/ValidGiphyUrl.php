<?php

namespace App\Rules;

use App\Support\GiphyUrl;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidGiphyUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! GiphyUrl::isValid($value)) {
            $fail('The :attribute must be a Giphy link such as giphy.com/gifs/..., media.giphy.com/..., or i.giphy.com/...');
        }
    }
}
