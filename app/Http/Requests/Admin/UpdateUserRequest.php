<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $id = $this->route('user') ? $this->route('user')->id : $this->id;
        return [
            'first_name' => ['required', 'string','max:255'],
            'last_name' => ['required', 'string','max:255'],
            'email' => ['required','email', 'unique:users,email,'.$id.',id,deleted_at,NULL'],
            'phone' => ['nullable','numeric','digits_between:6,15', 'unique:users,phone,'.$id.',id,deleted_at,NULL'],
            'postal_code' => ['nullable','numeric'],
            'dob' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $format = 'd/m/Y';
                    $parsedDate = \DateTime::createFromFormat($format, $value);
                    if (!$parsedDate || $parsedDate->format($format) !== $value) {
                        $fail("The $attribute must be a valid date in the format dd/mm/yyyy.");
                    }
                },
            ],
            'dni' => ['nullable','numeric'],
            'admission_date' => ['nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $format = 'd/m/Y';
                    $parsedDate = \DateTime::createFromFormat($format, $value);
                    if (!$parsedDate || $parsedDate->format($format) !== $value) {
                        $fail("The $attribute must be a valid date in the format dd/mm/yyyy.");
                    }
                },
            ],
        ];
    }
}
