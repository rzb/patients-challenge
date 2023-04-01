<?php

namespace Tests\Feature;

use App\Models\Patient;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function provideFilter(): Generator
    {
        yield 'Search by name' => ['name', fake()->unique()->name];

        yield 'Search by CPF' => ['cpf', '99999999999'];
    }
}
