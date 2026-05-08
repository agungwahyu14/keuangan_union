<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Category;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage-transactions');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in(['pemasukan', 'pengeluaran']),
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('is_active', true),
                // Validasi custom: tipe kategori harus cocok dengan tipe transaksi
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);
                    if ($category && $category->type !== $this->input('type')) {
                        $fail("Kategori tidak sesuai dengan tipe transaksi yang dipilih.");
                    }
                },
            ],
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:9999999999999.99',
            ],
            'transaction_date' => [
                'required',
                'date',
                'before_or_equal:today', // Tidak boleh future date
            ],
            'description' => [
                'required',
                'string',
                'max:255',
            ],
            'reference_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Custom validation messages dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'type.required'             => 'Tipe transaksi wajib dipilih.',
            'type.in'                   => 'Tipe transaksi tidak valid.',
            'category_id.required'      => 'Kategori wajib dipilih.',
            'category_id.exists'        => 'Kategori tidak ditemukan atau tidak aktif.',
            'amount.required'           => 'Jumlah nominal wajib diisi.',
            'amount.numeric'            => 'Jumlah nominal harus berupa angka.',
            'amount.min'                => 'Jumlah nominal minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date'     => 'Format tanggal tidak valid.',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh melebihi hari ini.',
            'description.required'      => 'Keterangan transaksi wajib diisi.',
            'description.max'           => 'Keterangan maksimal 255 karakter.',
            'reference_number.max'      => 'No. referensi maksimal 50 karakter.',
            'note.max'                  => 'Catatan maksimal 500 karakter.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'type'             => 'tipe transaksi',
            'category_id'      => 'kategori',
            'amount'           => 'jumlah nominal',
            'transaction_date' => 'tanggal transaksi',
            'description'      => 'keterangan',
            'reference_number' => 'no. referensi',
            'note'             => 'catatan',
        ];
    }
}
