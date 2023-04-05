<?php

namespace App\Clients\Cep;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ViaCep implements CepClient
{
    protected const BASE_URL = 'https://viacep.com.br/ws/';

    public function find(string $cep): CepResponse
    {
        $response = Http::get($this->endpoint($cep));

        return new CepResponse(
            cep: Str::removeNonDigits($response->json('cep')),
            street: $response->json('logradouro'),
            complement: $response->json('complemento'),
            neighborhood: $response->json('bairro'),
            city: $response->json('localidade'),
            uf: $response->json('uf')
        );
    }

    protected function endpoint(string ...$options): string
    {
        $options = implode('/', $options);

        return self::BASE_URL . "/$options/json";
    }
}
