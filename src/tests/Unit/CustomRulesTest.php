<?php

namespace Tests\Unit;

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
     * @dataProvider provideCns
     */
    public function it_validates_a_field($rule, $value, $shouldFail, $message): void
    {
        $this->expectExceptionOnlyIf($shouldFail, $message);

        $rule->validate('whatever', $value, $this->fail);
    }

    private function provideCpf(): Generator
    {
        yield 'valid number' => [new Cpf(), '20501792007', false, null];

        yield 'invalid repeating digits' => [new Cpf(), '99999999999', true, 'validation.cpf'];

        yield 'invalid check digit fails' => [new Cpf(), '20501792008', true, 'validation.cpf'];

        yield 'invalid too short' => [new Cpf(), '2050179200', true, 'validation.cpf'];

        yield 'invalid too long' => [new Cpf(), '205017920073', true, 'validation.cpf'];
    }

    private function provideCns(): Generator
    {
        yield 'valid number' => [new Cns(), '120413834000008', false, null];

        yield 'invalid number' => [new Cns(), '183839901480018', true, 'validation.cns'];
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
