<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKehadiranRequest extends FormRequest
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
            'tanggal' => 'required|date',
            'status' => 'required|string|in:Hadir,Sakit,Izin,Alpa,sakit,izin,alpa,hadir',
        ];
    }
}
