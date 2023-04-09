<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

class Cpf implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->isValid($value)) {
            $fail('validation.cpf')->translate();
        }
    }

    private function isValid(string $cpf): bool
    {
        $cpf = Str::of($cpf);

        if ($cpf->length() != 11 || $cpf->isMatch("/^{$cpf[0]}{11}$/")) {
            return false;
        }

        if (! $this->checkDigitsPass($cpf)) {
            return false;
        }

        return true;
    }

    private function checkDigitsPass(string $cpf): bool
    {
        for ($i = 9, $sum = 0; $i < 11; $i++) {
            for ($j = 0, $sum = 0; $j < $i; $j++) {
                $sum += $cpf[$j] * (($i + 1) - $j);
            }

            $sum = ((10 * $sum) % 11) % 10;

            if ($cpf[$i] != $sum) {
                return false;
            }
        }

        return true;
    }
}
