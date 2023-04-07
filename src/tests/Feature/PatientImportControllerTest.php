<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
    public function it_queues_the_import(): void
    {
        Excel::fake();
        $import = UploadedFile::fake()->create('patients.csv');

        $this->postJson('/api/imports', compact('import'));

        Excel::assertQueued($import->getFilename());
    }
}
