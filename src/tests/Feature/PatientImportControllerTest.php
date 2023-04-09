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
        $import = new UploadedFile(
            base_path('tests/patients.csv'),
            'patients.csv',
            'test/csv',
            null,
            true
        );

        $response = $this->postJson('/api/imports', compact('import'));

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
        $import = new UploadedFile(
            base_path('tests/patients.csv'),
            'patients.csv',
            'test/csv',
            null,
            true
        );
        Log::shouldReceive('error')->times(3);

        $response = $this->postJson('/api/imports', compact('import'));

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
}
