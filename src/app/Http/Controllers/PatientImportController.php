<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportPatientRequest;
use App\Imports\PatientsImport;
use Illuminate\Http\Response;

class PatientImportController extends Controller
{
    public function store(ImportPatientRequest $request): Response
    {
        (new PatientsImport())->queue($request->file('import'));

        return response()->noContent();
    }
}
