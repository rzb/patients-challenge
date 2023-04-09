<?php

namespace App\Clients\Cep;

interface CepClient
{
    public function find(string $cep): CepResponse|false;
}
