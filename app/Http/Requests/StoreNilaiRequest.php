<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNilaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_siswa' => 'required|string|exists:siswa,id_siswa',
            'id_jadwal' => 'required|string|exists:jadwal_mengajar,id_jadwal',
            'tugas1' => 'nullable|numeric|min:0|max:100',
            'tugas2' => 'nullable|numeric|min:0|max:100',
            'tugas3' => 'nullable|numeric|min:0|max:100',
            'tugas4' => 'nullable|numeric|min:0|max:100',
            'tugas5' => 'nullable|numeric|min:0|max:100',
            'uts' => 'nullable|numeric|min:0|max:100',
            'uas' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
