<?php

namespace App\Http\Controllers;

use App\Clients\Cep\CepClient;
use Illuminate\Http\JsonResponse;

class FindAddressByCep extends Controller
{
    public function __invoke(string $cep, CepClient $client): JsonResponse
    {
        $address = $client->find($cep);

        return response()->json(['data' => $address], $address ? 200 : 422);
    }
}
