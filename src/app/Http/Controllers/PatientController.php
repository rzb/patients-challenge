<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PatientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PatientResource::collection(
            Patient::when($request->input('term'), fn ($query, $term) => $query
                ->where('name', $term)
                ->orWhere('cpf', $term)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }
}
