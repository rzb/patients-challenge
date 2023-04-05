<?php

namespace App\Clients\Cep;

class CepResponse
{
    public function __construct(
        public string $cep,
        public string $street,
        public string $complement,
        public string $neighborhood,
        public string $city,
        public string $uf,
    )
    {}
}
