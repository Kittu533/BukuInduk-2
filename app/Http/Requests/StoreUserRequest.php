<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'username' => 'required|string|max:25|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,guru,orangtua',
            'id_guru' => 'nullable|string|exists:guru,id_guru',
            'id_siswa' => 'nullable|string|exists:siswa,id_siswa',
        ];
    }
}
