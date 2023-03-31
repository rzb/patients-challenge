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
        yield 'Valid number starting with "1" passes' => [true, '193839901480018'];

        yield 'Valid number starting with "2" passes' => [true, '209720193910004'];

        yield 'Valid number starting with "7" passes' => [true, '767629468350002'];

        yield 'Valid number starting with "8" passes' => [true, '849319684070005'];

        yield 'Valid number starting with "9" passes' => [true, '941262642390007'];
    }

    public function provideInvalidCns(): Generator
    {
        yield 'Invalid number starting with "1" fails' => [false, '183839901480018'];

        yield 'Invalid number starting with "2" fails' => [false, '219720193910004'];

        yield 'Invalid number starting with "7" fails' => [false, '777629468350002'];

        yield 'Invalid number starting with "8" fails' => [false, '839319684070005'];

        yield 'Invalid number starting with "9" fails' => [false, '931262642390007'];
    }
}
