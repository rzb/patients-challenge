<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class Cep implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cep = Str::of($value);

        if ($cep->length() !== 8 || $cep->startsWith('0')) {
            $fail('validation.cep')->translate();
        }
    }
}
