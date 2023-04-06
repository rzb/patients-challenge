<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class IndexPatientsRequest extends FormRequest
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
        // For flexibility, we accept both masked and unmasked CPFs without
        // messing with the search term too much, as we want to support
        // search for names as well. White spaces are left untouched.
        $this->whenFilled('term', fn () => $this->merge([
            'term' => Str::of($this->term)->remove('.')->remove('-')->value(),
        ]));
    }

    public function rules(): array
    {
        return [];
    }
}
