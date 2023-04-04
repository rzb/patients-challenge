<?php

namespace Tests\Unit;

use Illuminate\Support\Str;
use Tests\TestCase;

class StrTest extends TestCase
{
    /** @test */
    public function it_removes_non_digits_from_string(): void
    {
        $string = 'jkasd-./|*12i345-6+789p[pa';

        $digits = Str::removeNonDigits($string);

        $this->assertEquals('123456789', $digits);
    }

    /** @test */
    public function it_removes_non_digits_from_string_fluently(): void
    {
        $string = 'jkasd-./|*12i345-6+789p[pa';

        $digits = Str::of($string)->removeNonDigits();

        $this->assertEquals('123456789', $digits);
    }
}
