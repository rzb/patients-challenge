<?php

namespace App\Imports;

use App\Models\Patient;
use App\Rules\Cns;
use App\Rules\Cpf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;

class PatientsImport implements
    OnEachRow,
    WithHeadingRow,
    WithChunkReading,
    ShouldQueue,
    WithValidation,
    WithEvents,
    SkipsOnFailure
{
    use Importable;
    use RegistersEventListeners;

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
            'numero' => (string) $row['numero'],
        ]);
    }

    public function rules(): array
    {
        return [
            'foto' => 'required|string|min:3|max:255',
            'nome' => 'required|string|min:3|max:255',
            'nome_da_mae' => 'required|string|min:3|max:255',
            'data_de_nascimento' => 'required|date_format:Y-m-d',
            'cpf' => ['required', new Unique('patients'), new Cpf()],
            'cns' => ['required', new Unique('patients'), new Cns()],
            'cep' => 'required|string|size:8',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:255',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'localidade' => 'required|string|max:255',
            'uf' => 'required|alpha:ascii|size:2', // @todo consider enum or lookup table validation
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $failures = collect($failures);

        Log::error(
            "Patients Import: a validation error was found and the row was skipped.",
            [
                'row' => $failures->first()->row(),
                'values' => $failures->first()->values(),
                'errors' => $failures->map(fn (Failure $failure) =>
                    $failure->errors()
                )->flatten()->toArray(),
            ]
        );
    }
}
