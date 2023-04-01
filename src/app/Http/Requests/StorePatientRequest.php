<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'picture' => 'required|image',
            'name' => 'required',
            'mothers_name' => 'required',
            'birthdate' => 'required|date_format:Y-m-d',
            'cpf' => 'required', // @todo validate CPF
            'cns' => 'required', // @todo validate CNS
            'address.cep' => 'required',
            'address.street' => 'required',
            'address.number' => 'required',
            'address.complement' => 'required',
            'address.neighborhood' => 'required',
            'address.city' => 'required',
            'address.uf' => 'required',
        ];
    }
}
