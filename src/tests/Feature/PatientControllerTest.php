<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Patient;
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

   /** @test */
    public function it_filters_patients_by_name()
    {
        $patient = Patient::factory()->create(['name' => 'Very Specific Name']);
        Patient::factory(9)->create(['name' => 'Not our job to test elasticsearch engine']);

        $response = $this->json('GET', '/api/patients', [
            'term' => $patient->name,
        ]);

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data', 1)
                ->where("data.0.name", $patient->name)
                ->etc()
            );
    }

    /** @test */
    public function it_filters_patients_by_cpf()
    {
        Patient::factory()->create(['cpf' => '35795836460']);
        Patient::factory(3)->sequence(
            ['cpf' => '28461634560'],
            ['cpf' => '88066802269'],
            ['cpf' => '69832182484'],
        )->create();

        $response = $this->json('GET', '/api/patients', [
            'term' => '357.958.364-60'
        ]);

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('data', 1)
                ->where("data.0.cpf", '357.958.364-60')
                ->etc()
            );
    }

    /** @test */
    public function it_shows_a_patient_by_id()
    {
        $patient = Patient::factory()->hasAddress([
            'cep' => '25099070'
        ])->create([
            'cpf' => '66687846230',
            'cns' => '220408484180003',
        ]);

        $response = $this->getJson("/api/patients/{$patient->id}");

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', fn (AssertableJson $json) => $json
                        ->whereAll([
                            'id' => $patient->id,
                            'picture' => Storage::url($patient->picture),
                            'name' => $patient->name,
                            'mothers_name' => $patient->mothers_name,
                            'birthdate' => $patient->birthdate->format('Y-m-d'),
                            'cpf' => '666.878.462-30',
                            'cns' => '220 4084 8418 0003',
                            'created_at' => $patient->created_at->toISOString(),
                            'address' => [
                                'cep' => '25099-070',
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
        $address = Address::factory()->make(['cep' => '17540580']);

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
        Storage::assertExists('pictures/' . $file->hashName());
    }

    /** @test */
    public function it_updates_a_patient()
    {
        Storage::fake();
        $oldPatient = Patient::factory()->hasAddress()->create();
        $patient = Patient::factory()->make([
            'cpf' => '43970832314',
            'cns' => '253497229460018',
        ]);
        $file = UploadedFile::fake()->image('new-image.jpg');
        $address = Address::factory()->make([
            'cep' => '12074676'
        ]);

        $response = $this->patchJson("/api/patients/{$oldPatient->id}", [
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
                            'picture' => Storage::url("pictures/{$file->hashName()}"),
                            'name' => $patient->name,
                            'mothers_name' => $patient->mothers_name,
                            'birthdate' => $patient->birthdate->format('Y-m-d'),
                            'cpf' => '439.708.323-14',
                            'cns' => '253 4972 2946 0018',
                            'created_at' => $oldPatient->created_at->toISOString(),
                            'address' => [
                                'cep' => '12074-676',
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
        Storage::assertExists('pictures/' . $file->hashName());
    }

    /** @test */
    public function it_updates_the_patients_address()
    {
        Storage::fake();
        $oldAddress = Address::factory()->forPatient()->create([
            'cep' => '12345678',
        ]);
        $address = Address::factory()->make([
            'cep' => '12074676'
        ]);

        $response = $this->patchJson("/api/patients/{$oldAddress->patient->id}", [
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
                        ->where('address', [
                            'cep' => '12074-676',
                            'street' => $address->street,
                            'number' => $address->number,
                            'complement' => $address->complement,
                            'neighborhood' => $address->neighborhood,
                            'city' => $address->city,
                            'uf' => $address->uf,
                        ])
                        ->etc(),
                    )
            );
    }

    /** @test */
    public function it_updates_a_patients_picture_deleting_the_old_one()
    {
        Storage::fake();
        $picture = UploadedFile::fake()->image('old-picture.jpg')->store('pictures');
        $patient = Patient::factory()->hasAddress()->create(compact('picture'));
        $newPicture = UploadedFile::fake()->image('new-image.jpg');

        $this->patchJson("/api/patients/{$patient->id}", [
            'picture' => $newPicture,
        ]);

        Storage::assertExists('pictures/' . $newPicture->hashName());
        Storage::assertMissing($picture);
    }

    /** @test */
    public function it_deletes_a_patient()
    {
        Storage::fake();
        $patient = Patient::factory()->hasAddress()->create();

        $response = $this->deleteJson("api/patients/{$patient->id}");

        $response->assertNoContent();
        $this
            ->assertDatabaseEmpty('patients')
            ->assertDatabaseEmpty('addresses');
        Storage::assertMissing('pictures/' . $patient->picture);
    }

    /** @test */
    public function it_deletes_a_patients_picture()
    {
        Storage::fake();
        $picture = UploadedFile::fake()->image('picture.jpg')->store('pictures');
        $patient = Patient::factory()->hasAddress()->create(compact('picture'));

        $this->deleteJson("/api/patients/{$patient->id}");

        Storage::assertMissing($picture);
    }
}
