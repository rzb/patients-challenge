<?php

namespace App\Http\Requests;

use App\Rules\Cns;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Unique;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => Str::removeNonDigits($this->input('cpf')),
            'cns' => Str::removeNonDigits($this->input('cns')),
            'address' => array_merge($this->input('address'), [
                'cep' => Str::removeNonDigits($this->input('address.cep'))
            ]),
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
            'picture' => ['required', File::image()->max(2 * 1024)],
            'name' => 'required|string|min:3|max:255',
            'mothers_name' => 'required|string|min:3|max:255',
            'birthdate' => 'required|date_format:Y-m-d',
            'cpf' => ['required', new Unique('patients'), new Cpf()],
            'cns' => ['required', new Unique('patients'), new Cns()],
            // Not validating CEP on remote. It would probably be cached at this
            // point if the UI hits our api for autocompleting, but worthless
            // if we accepted invalid CEPs from imports to avoid timeouts.
            'address.cep' => 'required|string|size:8',
            'address.street' => 'required|string|max:255',
            'address.number' => 'required|string|max:255',
            'address.complement' => 'nullable|string|max:255',
            'address.neighborhood' => 'required|string|max:255',
            'address.city' => 'required|string|max:255',
            'address.uf' => 'required|alpha:ascii|size:2', // @todo consider enum or lookup table validation
        ];
    }
}
