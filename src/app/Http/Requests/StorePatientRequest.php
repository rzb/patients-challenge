<?php

namespace App\Http\Requests;

use App\Rules\Cep;
use App\Rules\Cns;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cep' => Str::removeNonDigits($this->cep),
            'cns' => Str::removeNonDigits($this->cns),
            'cpf' => Str::removeNonDigits($this->cpf),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'picture' => 'required|image',
            'name' => 'required',
            'mothers_name' => 'required',
            'birthdate' => 'required|date_format:Y-m-d',
            'cpf' => ['required', new Cpf()],
            'cns' => ['required', new Cns()],
            'address.cep' => ['required', new Cep()],
            'address.street' => 'required',
            'address.number' => 'required',
            'address.complement' => 'required',
            'address.neighborhood' => 'required',
            'address.city' => 'required',
            'address.uf' => 'required|size:2', // @todo consider enum or lookup table validation
        ];
    }
}
