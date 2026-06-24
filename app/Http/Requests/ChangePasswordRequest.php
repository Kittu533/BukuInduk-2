<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
        ];
    }
}
