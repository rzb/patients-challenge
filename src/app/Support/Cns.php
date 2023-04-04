<?php

namespace App\Support;

use Illuminate\Support\Str;

class Cns
{
    public static function generate(): string
    {
        $cns = new static();

        do {
            $number = $cns->fromPis($cns->randomPis());
        } while (strlen($number) !== 15);

        return $number;
    }

    public static function validate(string $cns): bool
    {
        if (strlen($cns) != 15) {
            return false;
        }

        if (in_array($cns[0], [7, 8, 9])) {
            return (new static())->validateTemporaryCns($cns);
        }

        if (in_array($cns[0], [1, 2])) {
            return (new static())->validateCns($cns);
        }

        return false;
    }

    protected function validateTemporaryCns(string $cns): bool
    {
        return $this->sumDigits($cns) % 11 === 0;
    }

    protected function validateCns(string $cns): bool
    {
        return $cns === $this->fromPis(substr($cns, 0, 11));
    }

    protected function sumDigits(string $digits): int
    {
        return Str::of($digits)->split(1)->reduce(
            fn ($total, $value, $index) => $total + ($value * (15 - $index))
        );
    }

    protected function fromPis(string $pis): string
    {
        $sum = $this->sumDigits($pis);

        $remainder = $sum % 11;

        $digit = $remainder === 0 ? 0 : 11 - $remainder;

        $lastFour =  $digit === 10 ? '001' . 11 - (($sum+2) % 11) : '000' . $digit;

        return $pis . $lastFour;
    }

    protected function randomPis(): string
    {
        return
            $this->randomFirstDigit() .
            $this->randomNextFiveDigits() .
            $this->randomNextFiveDigits();
    }

    protected function randomFirstDigit(): string
    {
        $digit = floor(($this->random() * 3) + 1);

        if ($digit == 3) {
            $digit = floor(($this->random() * 3) + 7);
        }

        return (string) $digit;
    }

    protected function randomNextFiveDigits(): string
    {
        $digits = floor(($this->random() * 99999) + 1);

        return substr('0' . $digits, -5);
    }

    protected function random(): float|int
    {
        return mt_rand() / (mt_getrandmax() + 1);
    }
}
