<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_guru' => 'required|string|max:4|unique:guru,id_guru',
            'nama_guru' => 'required|string|max:150',
            'nip' => 'nullable|string|max:25',
            'jenis_kelamin' => 'required|string|max:25',
            'jabatan' => 'nullable|string|max:100',
            'tugas_mengajar' => 'nullable|string|max:100',
        ];
    }
}
