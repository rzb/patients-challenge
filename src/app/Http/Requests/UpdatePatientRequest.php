<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
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
            'picture' => 'image',
            'name' => '',
            'mothers_name' => '',
            'birthdate' => 'date_format:Y-m-d',
            'cpf' => '', // @todo validate CPF
            'cns' => '', // @todo validate CNS
            'address.cep' => '',
            'address.street' => '',
            'address.number' => '',
            'address.complement' => '',
            'address.neighborhood' => '',
            'address.city' => '',
            'address.uf' => '',
        ];
    }
}
