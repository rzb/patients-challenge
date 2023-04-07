<?php

namespace App\Http\Requests;

use App\Rules\Cep;
use App\Rules\Cns;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdatePatientRequest extends FormRequest
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
        foreach (['address.cep', 'cns', 'cpf'] as $key) {
            if (! $this->filled($key)) {
                return;
            }

            $this->merge([$key => Str::removeNonDigits($this->input($key))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'picture' => 'sometimes|required|image',
            'name' => 'sometimes|required',
            'mothers_name' => 'sometimes|required',
            'birthdate' => 'sometimes|required|date_format:Y-m-d',
            'cpf' => ['sometimes', 'required', new Cpf()],
            'cns' => ['sometimes', 'required', new Cns()],
            'address.cep' => ['sometimes', 'required', new Cep()],
            'address.street' => 'sometimes|required',
            'address.number' => 'sometimes|required',
            'address.complement' => 'sometimes|required',
            'address.neighborhood' => 'sometimes|required',
            'address.city' => 'sometimes|required',
            'address.uf' => 'sometimes|required|size:2', // @todo consider enum or lookup table validation
        ];
    }
}
