<x-app-layout>
@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-5">
            <h2 class="text-lg font-bold text-white">Edit Transaksi</h2>
            <p class="text-amber-100 text-sm mt-0.5">ID: #{{ $transaksi->id }} · {{ $transaksi->transaction_date->format('d/m/Y') }}</p>
        </div>

        <form method="POST" action="{{ route('transaksi.update', $transaksi) }}" class="p-6 space-y-5"
              x-data="transaksiForm({{ $categories->groupBy('type')->toJson() }}, '{{ $transaksi->type }}', {{ $transaksi->amount }}, {{ $transaksi->category_id }})">

            @csrf @method('PUT')

            {{-- Toggle Tipe --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Transaksi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="pemasukan" class="sr-only"
                               x-model="selectedType" @change="filterCategories()">
                        <div :class="selectedType === 'pemasukan'
                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-md'
                                : 'bg-white text-gray-600 border-gray-200 hover:border-emerald-300'"
                             class="border-2 rounded-xl p-4 text-center transition-all select-none">
                            <div class="text-2xl mb-1"><i data-lucide="arrow-up" class="w-6 h-6 mx-auto"></i></div>
                            <div class="text-sm font-semibold">Pemasukan</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="pengeluaran" class="sr-only"
                               x-model="selectedType" @change="filterCategories()">
                        <div :class="selectedType === 'pengeluaran'
                                ? 'bg-red-600 text-white border-red-600 shadow-md'
                                : 'bg-white text-gray-600 border-gray-200 hover:border-red-300'"
                             class="border-2 rounded-xl p-4 text-center transition-all select-none">
                            <div class="text-2xl mb-1"><i data-lucide="arrow-down" class="w-6 h-6 mx-auto"></i></div>
                            <div class="text-sm font-semibold">Pengeluaran</div>
                        </div>
                    </label>
                </div>
                @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" id="category_id"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- Pilih Kategori --</option>
                    <template x-for="cat in filteredCategories" :key="cat.id">
                        <option :value="cat.id" :selected="cat.id == selectedCategoryId"
                                x-text="cat.name + (cat.is_hpp ? ' (HPP)' : '')"></option>
                    </template>
                </select>
                @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Nominal --}}
            <div>
                <label for="amount_display" class="block text-sm font-semibold text-gray-700 mb-1">Nominal <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">Rp</span>
                    <input type="text" id="amount_display"
                           :value="formatRupiah(amount)"
                           @input="amount = parseRupiah($event.target.value); $event.target.value = formatRupiah(amount)"
                           class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-right font-semibold">
                    <input type="hidden" name="amount" :value="amount">
                </div>
                @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Tanggal --}}
            <div>
                <label for="transaction_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                <input type="date" name="transaction_date" id="transaction_date"
                       value="{{ old('transaction_date', $transaksi->transaction_date->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('transaction_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan <span class="text-red-500">*</span></label>
                <input type="text" name="description" id="description"
                       value="{{ old('description', $transaksi->description) }}"
                       maxlength="255"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- No. Referensi --}}
            <div>
                <label for="reference_number" class="block text-sm font-semibold text-gray-700 mb-1">No. Referensi <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="text" name="reference_number" id="reference_number"
                       value="{{ old('reference_number', $transaksi->reference_number) }}"
                       maxlength="50"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('reference_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label for="note" class="block text-sm font-semibold text-gray-700 mb-1">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="note" id="note" rows="3" maxlength="500"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('note', $transaksi->note) }}</textarea>
                @error('note')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('transaksi.index') }}"
                   class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Batal</a>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition-colors">
                    Perbarui Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function transaksiForm(allCategories, initType, initAmount, initCategoryId) {
    return {
        selectedType: initType || '',
        selectedCategoryId: initCategoryId || 0,
        amount: initAmount || 0,
        allCategories: allCategories,
        filteredCategories: [],

        init() { this.filterCategories(); },

        filterCategories() {
            this.filteredCategories = this.allCategories[this.selectedType] || [];
        },

        formatRupiah(val) {
            if (!val || val === 0) return '';
            return parseInt(val).toLocaleString('id-ID');
        },

        parseRupiah(str) {
            return parseInt(str.replace(/\./g, '').replace(/,/g, '')) || 0;
        }
    }
}
</script>
@endpush
</x-app-layout>
