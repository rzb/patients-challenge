<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexPatientsRequest;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PatientController extends Controller
{
    public function index(IndexPatientsRequest $request): AnonymousResourceCollection
    {
        return PatientResource::collection(
            Patient::when($request->has('term'), fn () =>
                Patient::search($request->input('term'))
            )->paginate($request->input('per_page'))
        );
    }

    public function store(StorePatientRequest $request): PatientResource
    {
        $patient = Patient::make($request->except('picture', 'address'));

        $patient->picture = $request->file('picture')->store('pictures');

        $patient->save();

        $patient->address()->create($request->input('address'));

        return PatientResource::make($patient->load('address'));
    }

    public function show(Patient $patient): PatientResource
    {
        return PatientResource::make($patient->load('address'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): PatientResource
    {
        $data = $request->except('picture', 'address');

        if ($request->hasFile('picture')) {
            $data['picture'] = $request->file('picture')->store('pictures');
        }

        $patient->update($data);

        $patient->address()->update($request->input('address'));

        return PatientResource::make($patient->load('address'));
    }

    public function destroy(Patient $patient): Response
    {
        $patient->delete();

        return response()->noContent();
    }
}
