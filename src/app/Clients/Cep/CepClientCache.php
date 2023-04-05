<?php

namespace App\Clients\Cep;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CepClientCache implements CepClient
{
    public function __construct(protected CepClient $client)
    {}

    public function find(string $cep): CepResponse|false
    {
        return Cache::remember(
            key: 'addresses.' . Str::removeNonDigits($cep),
            ttl: now()->addMonth(),
            callback: fn () => $this->client->find($cep)
        );
    }
}
