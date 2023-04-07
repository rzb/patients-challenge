<?php

namespace Tests\Feature;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PatientImportControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_imports_patients_from_uploaded_csv(): void
    {
        $response = $this->postJson('/api/imports', [
            'import' => $this->importFile('patients'),
        ]);

        $response->assertNoContent();
        $this->assertDatabaseCount('patients', 8);
    }

    /** @test */
    public function it_imports_patients_from_uploaded_csv_using_custom_headings(): void
    {
        $response = $this->postJson('/api/imports', [
            'import' => $this->importFile('patients-with-custom-headings'),
            'map' => [
                'picture' => 'foto',
                'name' => 'nome',
                'mothers_name' => 'nome_da_mae',
                'birthdate' => 'data_de_nascimento',
                'cpf' => 'cpf',
                'cns' => 'cns',
                'address.cep' => 'cep',
                'address.street' => 'logradouro',
                'address.number' => 'numero',
                'address.complement' => 'complemento',
                'address.neighborhood' => 'bairro',
                'address.city' => 'localidade',
                'address.uf' => 'uf',
            ],
        ]);

        $response->assertNoContent();
        $this->assertDatabaseCount('patients', 8);
    }

    /** @test */
    public function it_skips_invalid_rows_and_logs_the_errors(): void
    {
        // Pre-register some CPFs from the csv to trigger validation errors while importing...
        Patient::factory(3)->sequence(
            ['cpf' => '27240092666', 'cns' => '138034859940001'],
            ['cpf' => '25292040800'],
            ['cpf' => '28597408804'],
        )->create();
        Log::shouldReceive('error')->times(3);

        $response = $this->postJson('/api/imports', [
            'import' => $this->importFile('patients'),
        ]);

        $response->assertNoContent();
    }

    /** @test */
    public function it_queues_the_import(): void
    {
        Excel::fake();
        $import = UploadedFile::fake()->create('patients.csv');

        $this->postJson('/api/imports', compact('import'));

        Excel::assertQueued($import->getFilename());
    }

    protected function importFile(string $name): UploadedFile
    {
        return new UploadedFile(
            base_path("tests/{$name}.csv"),
            "{$name}.csv",
            'text/csv',
            null,
            true
        );
    }
}
