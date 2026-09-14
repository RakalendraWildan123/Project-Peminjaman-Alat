<?php

namespace App\Http\Requests\Alat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id' => [
                'required',
                'integer',
                Rule::exists('kategori', 'id')
            ],
            'nama_alat' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
            'status_kondisi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.exists' => 'kategori yang dipilih tidak valid atau tidak terdaftar.',
            'stok.min' => 'Stok Tidak boleh kurang dari 0. ',
            'gambar.max' => 'Ukuran gambar maksimal adalh 2 MB.',
            'gambar.image' => 'file yang diunggah harus berupa gambar.',
        ];
    }
}
