<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FindAddressByCepTest extends TestCase
{
    /** @test */
    public function it_validates_a_cep_and_returns_the_corresponding_full_address(): void
    {
        $cep = '01001-000';
        Http::fake(fn() => [
            'cep' => $cep,
            'logradouro' => 'Praça da Sé',
            'complemento' => 'lado ímpar',
            'bairro' => 'Sé',
            'localidade' => 'São Paulo',
            'uf' => 'SP',
            'ibge' => '3550308',
            'gia' => '1004',
            'ddd' => '11',
            'siafi' => '7107'
        ]);

        $response = $this->getJson("/api/services/addresses/$cep", compact('cep'));

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [
                    'cep' => Str::removeNonDigits($cep),
                    'street' => 'Praça da Sé',
                    'complement' => 'lado ímpar',
                    'neighborhood' => 'Sé',
                    'city' => 'São Paulo',
                    'uf' => 'SP',
                ])
            );
        Http::assertSentCount(1);
    }

    /** @test */
    public function it_fails_with_422_if_cep_is_invalid(): void
    {
        $cep = 'invalid-99999';
        Http::fake(fn() => false);

        $response = $this->getJson("/api/services/addresses/$cep", compact('cep'));

        $response
            ->assertUnprocessable()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', false)
            );
        Http::assertSentCount(1);
    }
}
