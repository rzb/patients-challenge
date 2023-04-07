<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexPatientsRequest;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PatientController extends Controller
{
    public function index(IndexPatientsRequest $request): AnonymousResourceCollection
    {
        return PatientResource::collection(
            Patient::when($request->has('term'), fn () =>
                Patient::search($request->input('term'))
            )->paginate($request->integer('per_page'))
        );
    }

    public function store(StorePatientRequest $request): PatientResource
    {
        $patient = $request->fulfill();

        return PatientResource::make($patient->load('address'));
    }

    public function show(Patient $patient): PatientResource
    {
        return PatientResource::make($patient->load('address'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): PatientResource
    {
        $patient = $request->fulfill();

        return PatientResource::make($patient->load('address'));
    }

    public function destroy(Patient $patient): Response
    {
        $patient->delete();

        return response()->noContent();
    }
}
