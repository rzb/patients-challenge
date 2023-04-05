<?php

namespace Tests\Clients;

use App\Clients\Cep\CepClient;
use App\Clients\Cep\CepResponse;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CepClientTest extends TestCase
{
    /**
     * @test
     * @group api
     */
    public function it_finds_an_address_by_cep(): void
    {
        $cep = '01001000';

        try {
            $response = app(CepClient::class)->find($cep);
        } catch (Exception $e) {
            $this->fail('Failed to hit the API. ' . $e->getMessage());
        }

        $this->assertInstanceOf(CepResponse::class, $response);
    }

    /**
     * @test
     * @group api
     */
    public function it_returns_false_if_cep_is_invalid(): void
    {
        $cep = 'invalid-9999999';

        try {
            $response = app(CepClient::class)->find($cep);
        } catch (Exception $e) {
            $this->fail('Failed to hit the API. ' . $e->getMessage());
        }

        $this->assertFalse($response);
    }

    /** @test */
    public function it_creates_the_normalized_address_response(): void
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

        $address = app(CepClient::class)->find($cep);

        $this->assertInstanceOf(CepResponse::class, $address);
        $this->assertEquals('01001000', $address->cep);
        $this->assertEquals('Praça da Sé', $address->street);
        $this->assertEquals('lado ímpar', $address->complement);
        $this->assertEquals('Sé', $address->neighborhood);
        $this->assertEquals('São Paulo', $address->city);
        $this->assertEquals('SP', $address->uf);
    }

    /** @test */
    public function it_caches_addresses()
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

        app(CepClient::class)->find($cep);

        $this->assertTrue(Cache::has('addresses.' . Str::removeNonDigits($cep)));
    }
}
