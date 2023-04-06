<?php

namespace App\Imports;

use App\Models\Patient;
use App\Rules\Cep;
use App\Rules\Cns;
use App\Rules\Cpf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class PatientsImport implements OnEachRow, WithHeadingRow, WithChunkReading, ShouldQueue, WithValidation
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

    public function prepareForValidation(array $row): array
    {
        return array_merge($row, [
            'cep' => Str::removeNonDigits($row['cep']),
            'cns' => Str::removeNonDigits($row['cns']),
            'cpf' => Str::removeNonDigits($row['cpf']),
        ]);
    }

    public function rules(): array
    {
        return [
            'foto' => 'required',
            'nome' => 'required',
            'nome_da_mae' => 'required',
            'data_de_nascimento' => 'required|date_format:Y-m-d',
            'cpf' => ['required', new Cpf()],
            'cns' => ['required', new Cns()],
            'cep' => ['required', new Cep()],
            'logradouro' => 'required',
            'numero' => 'required',
            'complemento' => 'nullable',
            'bairro' => 'required',
            'localidade' => 'required',
            'uf' => 'required|size:2', // @todo consider enum or lookup table validation
        ];
    }
}
