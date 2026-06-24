<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => 'required|string|max:50',
            'username' => 'required|string|max:25|unique:users,username,' . $userId . ',id_users',
            'email' => 'required|email|max:100|unique:users,email,' . $userId . ',id_users',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,guru,orangtua',
            'id_guru' => 'nullable|string|exists:guru,id_guru',
            'id_siswa' => 'nullable|string|exists:siswa,id_siswa',
        ];
    }
}
