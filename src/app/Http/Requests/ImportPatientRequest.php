<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ImportPatientRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'import' => 'required|file|mimetypes:text/csv',
            // Optional headings map for custom import templates. Omit to use default.
            'map.picture' => 'sometimes|required|string',
            'map.name' => 'sometimes|required|string',
            'map.mothers_name' => 'sometimes|required|string',
            'map.birthdate' => 'sometimes|required|string',
            'map.cpf' => 'sometimes|required|string',
            'map.cns' => 'sometimes|required|string',
            'map.address.cep' => 'sometimes|required|string',
            'map.address.street' => 'sometimes|required|string',
            'map.address.number' => 'sometimes|required|string',
            'map.address.complement' => 'sometimes|required|string',
            'map.address.neighborhood' => 'sometimes|required|string',
            'map.address.city' => 'sometimes|required|string',
        ];
    }
}
