<?php

namespace Tests\Unit;

use App\Support\Cns;
use Generator;
use PHPUnit\Framework\TestCase;

class CnsTest extends TestCase
{
    /** @test */
    public function it_generates_a_valid_cns(): void
    {
        $this->assertTrue(Cns::validate(Cns::generate()));
    }

    /**
     * @test
     * @dataProvider provideValidCns
     * @dataProvider provideInvalidCns
     */
    public function it_validates_a_cns($isValid, $cns): void
    {
        $this->assertEquals($isValid, Cns::validate($cns));
    }

    public function provideValidCns(): Generator
    {
        yield 'valid number starting with "1"' => [true, '193839901480018'];

        yield 'valid number starting with "2"' => [true, '209720193910004'];

        yield 'valid number starting with "7"' => [true, '767629468350002'];

        yield 'valid number starting with "8"' => [true, '849319684070005'];

        yield 'valid number starting with "9"' => [true, '941262642390007'];
    }

    public function provideInvalidCns(): Generator
    {
        yield 'invalid number with wrong length' => [false, '1838399014800180'];

        yield 'invalid number starting with "1"' => [false, '183839901480018'];

        yield 'invalid number starting with "2"' => [false, '219720193910004'];

        yield 'invalid number starting with "7"' => [false, '777629468350002'];

        yield 'invalid number starting with "8"' => [false, '839319684070005'];

        yield 'invalid number starting with "9"' => [false, '931262642390007'];

        yield 'invalid number starting with invalid digit' => [false, '483839901480018'];
    }
}
