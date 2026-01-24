<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\s\-]+$/u', // len písmená, medzery a pomlčky
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:' . User::class,
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
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
            'email.unique' => 'Tento email je už zaregistrovaný.',

            'password.required' => 'Heslo je povinné.',
            'password.min' => 'Heslo musí mať minimálne 8 znakov.',
            'password.confirmed' => 'Heslá sa nezhodujú.',
            'password.mixed' => 'Heslo musí obsahovať veľké aj malé písmená.',
            'password.numbers' => 'Heslo musí obsahovať aspoň jedno číslo.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'meno',
            'email' => 'email',
            'password' => 'heslo',
            'password_confirmation' => 'potvrdenie hesla',
        ];
    }
}

