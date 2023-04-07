<?php

namespace App\Http\Requests;

use App\Rules\Cep;
use App\Rules\Cns;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Unique;

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
        $data = $this->all();

        foreach (['address.cep', 'cns', 'cpf'] as $key) {
            if (! $this->filled($key)) {
                return;
            }

            Arr::set($data, $key, Str::removeNonDigits($this->input($key)));
        }

        $this->merge($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'picture' => ['sometimes', 'required', File::image()->max(2 * 1024)],
            'name' => 'sometimes|required|string|min:3|max:255',
            'mothers_name' => 'sometimes|required|string|min:3|max:255',
            'birthdate' => 'sometimes|required|date_format:Y-m-d',
            'cpf' => [
                'sometimes',
                'required',
                (new Unique('patients'))->ignoreModel($this->route()->patient),
                new Cpf(),
            ],
            'cns' => [
                'sometimes',
                'required',
                (new Unique('patients'))->ignoreModel($this->route()->patient),
                new Cns(),
            ],
            'address.cep' => ['sometimes', 'required', new Cep()],
            'address.street' => 'sometimes|required|string|max:255',
            'address.number' => 'sometimes|required|string|max:255',
            'address.complement' => 'nullable|string|max:255',
            'address.neighborhood' => 'sometimes|required|string|max:255',
            'address.city' => 'sometimes|required|string|max:255',
            'address.uf' => 'sometimes|required|alpha:ascii|size:2', // @todo consider enum or lookup table validation
        ];
    }
}
