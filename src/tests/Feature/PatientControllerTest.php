<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Patient;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_patients(): void
    {
        Patient::factory(10)->create();

        $response = $this->getJson('/api/patients');

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 10, fn ($json) => $json
                    ->hasAll(
                        'id',
                        'picture',
                        'name',
                        'mothers_name',
                        'birthdate',
                        'cpf',
                        'cns',
                        'created_at',
                    )
                )->etc()
            );
    }

    /**
     * @test
     * @dataProvider providePagination
     */
    public function it_paginates_patients($expectedPerPage, $per_page)
    {
        Patient::factory($expectedPerPage + 2)->create();

        $response = $this->json('GET', '/api/patients', compact('per_page'));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', $expectedPerPage)
                ->hasAll('links', 'meta')
                ->etc(),
            );
    }

       /**
        * @test
        * @dataProvider provideFilter
        */
    public function it_filters_patients($column, $term)
    {
        Patient::factory()->create([$column => $term]);
        Patient::factory(9)->create();

        $response = $this->json('GET', '/api/patients', compact('term'));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data', 1)
                ->where("data.0.$column", $term)
                ->etc()
            );
    }

    /** @test */
    public function it_shows_a_patient_by_id()
    {
        $patient = Patient::factory()->hasAddress()->create();

        $response = $this->getJson("/api/patients/{$patient->id}");

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', fn (AssertableJson $json) => $json
                        ->whereAll([
                            'id' => $patient->id,
                            'picture' => $patient->picture,
                            'name' => $patient->name,
                            'mothers_name' => $patient->mothers_name,
                            'birthdate' => $patient->birthdate->format('Y-m-d'),
                            'cpf' => $patient->cpf,
                            'cns' => $patient->cns,
                            'created_at' => $patient->created_at->toISOString(),
                            'address' => [
                                'cep' => $patient->address->cep,
                                'street' => $patient->address->street,
                                'number' => $patient->address->number,
                                'complement' => $patient->address->complement,
                                'neighborhood' => $patient->address->neighborhood,
                                'city' => $patient->address->city,
                                'uf' => $patient->address->uf,
                            ],
                        ])
                        ->etc(),
                    )
            );
    }

    /** @test */
    public function it_stores_a_patient()
    {
        Storage::fake();
        $patient = Patient::factory()->make();
        $file = UploadedFile::fake()->image($patient->picture);
        $address = Address::factory()->make();

        $response = $this->postJson('/api/patients', [
            'picture' => $file,
            'name' => $patient->name,
            'mothers_name' => $patient->mothers_name,
            'birthdate' => $patient->birthdate->format('Y-m-d'),
            'cpf' => $patient->cpf,
            'cns' => $patient->cns,
            'address' => [
                'cep' => $address->cep,
                'street' => $address->street,
                'number' => $address->number,
                'complement' => $address->complement,
                'neighborhood' => $address->neighborhood,
                'city' => $address->city,
                'uf' => $address->uf,
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json) => $json->has('data.id'));
        $this->assertDatabaseCount('patients', 1);
        Storage::disk()->assertExists('pictures/' . $file->hashName());
    }

    /** @test */
    public function it_updates_a_patient()
    {
        Storage::fake();
        $oldPatient = Patient::factory()->hasAddress()->create();
        $patient = Patient::factory()->make();
        $file = UploadedFile::fake()->image('new-image.jpg');
        $address = Address::factory()->make();

        $response = $this->putJson("/api/patients/{$oldPatient->id}", [
            'picture' => $file,
            'name' => $patient->name,
            'mothers_name' => $patient->mothers_name,
            'birthdate' => $patient->birthdate->format('Y-m-d'),
            'cpf' => $patient->cpf,
            'cns' => $patient->cns,
            'address' => [
                'cep' => $address->cep,
                'street' => $address->street,
                'number' => $address->number,
                'complement' => $address->complement,
                'neighborhood' => $address->neighborhood,
                'city' => $address->city,
                'uf' => $address->uf,
            ],
        ]);

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', fn (AssertableJson $json) => $json
                        ->whereAll([
                            'id' => $oldPatient->id,
                            'picture' => 'pictures/' . $file->hashName(),
                            'name' => $patient->name,
                            'mothers_name' => $patient->mothers_name,
                            'birthdate' => $patient->birthdate->format('Y-m-d'),
                            'cpf' => $patient->cpf,
                            'cns' => $patient->cns,
                            'created_at' => $oldPatient->created_at->toISOString(),
                            'address' => [
                                'cep' => $address->cep,
                                'street' => $address->street,
                                'number' => $address->number,
                                'complement' => $address->complement,
                                'neighborhood' => $address->neighborhood,
                                'city' => $address->city,
                                'uf' => $address->uf,
                            ],
                        ])
                        ->etc(),
                    )
            );
        Storage::disk()->assertExists('pictures/' . $file->hashName());
    }

    /** @test */
    public function it_deletes_a_patient()
    {
        $patient = Patient::factory()->hasAddress()->create();

        $response = $this->deleteJson("api/patients/{$patient->id}");

        $response->assertNoContent();
        $this
            ->assertDatabaseEmpty('patients')
            ->assertDatabaseEmpty('addresses');
    }

    public function provideFilter(): Generator
    {
        yield 'name' => ['name', fake()->unique()->name];

        yield 'cpf' => ['cpf', '99999999999'];
    }
}
