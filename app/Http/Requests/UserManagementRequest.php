<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementRequest extends FormRequest
{
    /**
     * Hanya Admin yang bisa manage user.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage-users');
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        $userId = $this->route('pengguna')?->id ?? $this->route('pengguna');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'role' => [
                'required',
                // Admin hanya bisa tambah Petugas (bukan Admin lain)
                Rule::in(['petugas']),
            ],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    /**
     * Handle boolean fields dari form.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
        ]);
    }

    /**
     * Custom messages dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.max'           => 'Nama maksimal 100 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan oleh user lain.',
            'role.required'      => 'Role wajib dipilih.',
            'role.in'            => 'Role tidak valid. Hanya Petugas yang bisa ditambahkan.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nama lengkap',
            'email'    => 'email',
            'phone'    => 'no. telepon',
            'role'     => 'role',
            'password' => 'password',
        ];
    }
}
