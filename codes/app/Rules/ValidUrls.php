<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidUrls implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $urls = preg_split('/\s+/', $value);

        foreach ($urls as $url) {
            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                $fail('The :attribute must contain valid URLs.');

                return;
            }
        }
    }

    public function message(): string
    {
        return 'The :attribute must contain valid URLs.';
    }
}
