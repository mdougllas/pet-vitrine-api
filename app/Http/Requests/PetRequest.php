<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetRequest extends FormRequest
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
            'distance' => ['required', 'integer', 'min:1', 'max:100'],
            'limit' => ['required', 'integer', 'min:1', 'max:100'],
            'location' => ['nullable'],
            'page' => ['required', 'integer', 'min:1'],
        ];
    }
}
