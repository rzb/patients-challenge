<?php

namespace App\Imports;

use App\Models\Patient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class PatientsImport implements OnEachRow, WithHeadingRow, WithChunkReading, ShouldQueue
{
    use Importable;

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        $patient = Patient::create([
            'picture' => $row['foto'],
            'name' => $row['nome'],
            'mothers_name' => $row['nome_da_mae'],
            'birthdate' => $row['data_de_nascimento'],
            'cpf' => $row['cpf'],
            'cns' => $row['cns'],
        ]);

        $patient->address()->create([
            'cep' => $row['cep'],
            'street' => $row['logradouro'],
            'number' => $row['numero'],
            'complement' => $row['complemento'],
            'neighborhood' => $row['bairro'],
            'city' => $row['localidade'],
            'uf' => $row['uf'],
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
