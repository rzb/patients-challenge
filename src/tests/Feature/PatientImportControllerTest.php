<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

use function Pest\testDirectory;

class PatientImportControllerTest extends TestCase
{
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

        $v = Validator::make([], []);

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
