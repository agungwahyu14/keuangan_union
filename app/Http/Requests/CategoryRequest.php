<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Hanya Admin yang bisa manage kategori.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage-categories');
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        $categoryId = $this->route('kategori')?->id ?? $this->route('kategori');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                // Nama unik, kecuali untuk update kategori yang sama
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'type' => [
                'required',
                Rule::in(['pemasukan', 'pengeluaran']),
            ],
            'is_hpp' => [
                'boolean',
                // is_hpp hanya boleh true jika type = pengeluaran
                function ($attribute, $value, $fail) {
                    if ($value && $this->input('type') === 'pemasukan') {
                        $fail('Flag HPP hanya berlaku untuk kategori pengeluaran.');
                    }
                },
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    /**
     * Prepare input — handle checkbox boolean values.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_hpp'    => $this->boolean('is_hpp'),
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
        ]);
    }

    /**
     * Custom messages dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'Nama kategori wajib diisi.',
            'name.max'       => 'Nama kategori maksimal 100 karakter.',
            'name.unique'    => 'Nama kategori sudah digunakan.',
            'type.required'  => 'Tipe kategori wajib dipilih.',
            'type.in'        => 'Tipe kategori tidak valid.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => 'nama kategori',
            'type'        => 'tipe kategori',
            'is_hpp'      => 'flag HPP',
            'description' => 'deskripsi',
            'is_active'   => 'status aktif',
        ];
    }
}
