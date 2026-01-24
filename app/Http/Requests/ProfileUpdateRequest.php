<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Meno je povinné.',
            'name.min' => 'Meno musí mať minimálne 2 znaky.',
            'name.max' => 'Meno môže mať maximálne 255 znakov.',
            'name.regex' => 'Meno môže obsahovať len písmená, medzery a pomlčky.',

            'email.required' => 'Email je povinný.',
            'email.email' => 'Zadajte platnú emailovú adresu.',
            'email.max' => 'Email môže mať maximálne 255 znakov.',
            'email.unique' => 'Tento email je už používaný iným účtom.',
        ];
    }
}
