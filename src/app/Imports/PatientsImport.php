<?php

namespace App\Imports;

use App\Models\Patient;
use App\Rules\Cns;
use App\Rules\Cpf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
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

    public function __construct(protected null|RowMapper $mapper = null)
    {}

    public static function usingMap(array $map): static
    {
        return new static(new RowMapper($map));
    }

    public function onRow(Row $row)
    {
        $row = $row->toCollection();

        $patient = Patient::create($row->except('address')->toArray());

        $patient->address()->create($row->get('address'));
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function prepareForValidation(array $row): array
    {
        $row = $this->mapRow($row);

        return $row->replace([
            'cpf' => Str::removeNonDigits($row->get('cpf')),
            'cns' => Str::removeNonDigits($row->get('cns')),
            'address.cep' => Str::removeNonDigits($row->get('address.cep')),
            'address.number' => (string) $row->get('address.number')
        ])->undot()->toArray();
    }

    public function rules(): array
    {
        return [
            'picture' => 'required|string|min:3|max:255',
            'name' => 'required|string|min:3|max:255',
            'mothers_name' => 'required|string|min:3|max:255',
            'birthdate' => 'required|date_format:Y-m-d',
            'cpf' => ['required', new Unique('patients'), new Cpf()],
            'cns' => ['required', new Unique('patients'), new Cns()],
            'address.cep' => 'required|string|size:8',
            'address.street' => 'required|string|max:255',
            'address.number' => 'required|string|max:255',
            'address.complement' => 'nullable|string|max:255',
            'address.neighborhood' => 'required|string|max:255',
            'address.city' => 'required|string|max:255',
            'address.uf' => 'required|alpha:ascii|size:2', // @todo consider enum or lookup table validation
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $failures = new Collection($failures);

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

    protected function mapRow(array $row): Collection
    {
        return $this->mapper?->handle($row) ?: new Collection($row);
    }
}
