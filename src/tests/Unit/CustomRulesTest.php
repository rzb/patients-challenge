<?php

namespace Tests\Unit;

use App\Rules\Cep;
use App\Rules\Cns;
use App\Rules\Cpf;
use Closure;
use Exception;
use Generator;
use PHPUnit\Framework\TestCase;

class CustomRulesTest extends TestCase
{
    protected Closure $fail;

    protected function setUp(): void
    {
        $this->fail = fn (string $message) => throw new Exception($message);
    }

    /**
     * @test
     * @dataProvider provideCpf
     */
    public function it_validates_cpf($value, $shouldFail, $message): void
    {
        $this->expectExceptionOnlyIf($shouldFail, $message);

        (new Cpf())->validate('cpf', $value, $this->fail);
    }

    /**
     * @test
     * @dataProvider provideCns
     */
    public function it_validates_cns($value, $shouldFail, $message): void
    {
        $this->expectExceptionOnlyIf($shouldFail, $message);

        (new Cns())->validate('cns', $value, $this->fail);
    }

    /**
     * @test
     * @dataProvider provideCep
     */
    public function it_validates_cep($value, $shouldFail, $message): void
    {
        $this->expectExceptionOnlyIf($shouldFail, $message);

        (new Cep())->validate('cep', $value, $this->fail);
    }

    private function provideCpf(): Generator
    {
        yield 'valid with digits only' => ['20501792007', false, null];

        yield 'invalid repeating digits' => ['99999999999', true, 'validation.cpf'];

        yield 'invalid check digit fails' => ['20501792008', true, 'validation.cpf'];

        yield 'invalid too short' => ['2050179200', true, 'validation.cpf'];

        yield 'invalid too long' => ['205017920073', true, 'validation.cpf'];
    }

    private function provideCns(): Generator
    {
        yield 'valid with digits only' => ['120413834000008', false, null];

        yield 'invalid too short' => ['2050179200', true, 'validation.cns'];

        yield 'invalid too long' => ['205017920073', true, 'validation.cns'];
    }

    private function provideCep(): Generator
    {
        yield 'valid with digits only' => ['69440970', false, null];

        yield 'invalid too short' => ['6944097', true, 'validation.cep'];

        yield 'invalid too long' => ['694409702', true, 'validation.cep'];
    }

    protected function expectExceptionOnlyIf(bool $condition, ?string $message)
    {
        if ($condition) {
            $this->expectException(Exception::class);

            $this->expectExceptionMessage($message);

            return;
        }

        $this->expectNotToPerformAssertions();
    }
}
