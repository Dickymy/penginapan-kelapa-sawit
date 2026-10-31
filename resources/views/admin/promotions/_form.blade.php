<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Promo <span class="text-red-500">*</span></label>
        <input type="text" name="code" value="{{ old('code', $promotion->code ?? '') }}" required maxlength="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase font-mono focus:ring-primary-500 focus:border-primary-500" placeholder="Contoh: DISKON10">
        @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $promotion->name ?? '') }}" required maxlength="150" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Contoh: Diskon Menginap 10%">
        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Keterangan singkat tentang promo ini...">{{ old('description', $promotion->description ?? '') }}</textarea>
    @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Diskon <span class="text-red-500">*</span></label>
        <select name="type" id="promo_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500"
                x-data x-on:change="$dispatch('promo-type-changed', { value: $el.value })">
            <option value="percentage" {{ old('type', isset($promotion) ? ($promotion->type->value ?? $promotion->type) : 'percentage') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
            <option value="fixed" {{ old('type', isset($promotion) ? ($promotion->type->value ?? $promotion->type) : 'percentage') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
        </select>
        @error('type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div x-data="{ type: '{{ old('type', isset($promotion) ? ($promotion->type->value ?? $promotion->type) : 'percentage') }}' }" x-on:promo-type-changed.window="type = $event.detail.value">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Nilai <span class="text-gray-400 font-normal" x-text="type === 'percentage' ? '(%)' : '(Rupiah)'"></span> <span class="text-red-500">*</span>
        </label>
        <input type="number" name="value" value="{{ old('value', $promotion->value ?? '') }}" required min="1" :max="type === 'percentage' ? 100 : null" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500"
               :placeholder="type === 'percentage' ? 'Contoh: 10' : 'Contoh: 50000'">
        <p class="text-xs text-gray-400 mt-1" x-show="type === 'percentage'" x-cloak>Masukkan angka persen (1 - 100), misal: 10 untuk diskon 10%</p>
        <p class="text-xs text-gray-400 mt-1" x-show="type === 'fixed'" x-cloak>Masukkan nominal Rupiah, misal: 50000 untuk diskon Rp50.000</p>
        @error('value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Berlaku <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($promotion) && $promotion->starts_at ? $promotion->starts_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        @error('starts_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Berakhir Pada <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', isset($promotion) && $promotion->ends_at ? $promotion->ends_at->format('Y-m-d\TH:i') : now()->addMonth()->format('Y-m-d\TH:i')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        @error('ends_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Booking (Rp)</label>
        <input type="number" name="minimum_booking_amount" value="{{ old('minimum_booking_amount', $promotion->minimum_booking_amount ?? 0) }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="0">
        <p class="text-xs text-gray-400 mt-1">Isi 0 jika tidak ada syarat minimum belanja.</p>
        @error('minimum_booking_amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Maks. Diskon (Rp, opsional)</label>
        <input type="number" name="maximum_discount" value="{{ old('maximum_discount', $promotion->maximum_discount ?? '') }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Kosongkan jika tanpa batas">
        <p class="text-xs text-gray-400 mt-1">Batas potongan tertinggi (berguna untuk tipe persentase).</p>
        @error('maximum_discount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Penggunaan Total (opsional)</label>
        <input type="number" name="usage_quota" value="{{ old('usage_quota', $promotion->usage_quota ?? '') }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Kosongkan jika tak terbatas">
        @error('usage_quota') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Maks. per Pengguna (opsional)</label>
        <input type="number" name="max_usage_per_user" value="{{ old('max_usage_per_user', $promotion->max_usage_per_user ?? '') }}" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Kosongkan jika tak terbatas">
        @error('max_usage_per_user') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="pt-4 border-t border-gray-100 space-y-4">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', isset($promotion) ? $promotion->is_active : true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
        </div>
        <div class="ml-3 text-sm">
            <label for="is_active" class="font-medium text-gray-700 cursor-pointer">Aktifkan promo ini</label>
            <p class="text-xs text-gray-500">Hanya promo aktif dan dalam periode berlaku yang dapat digunakan.</p>
        </div>
    </div>

    <div class="flex items-start bg-emerald-50/60 border border-emerald-100 rounded-xl p-3.5">
        <div class="flex items-center h-5">
            <input type="hidden" name="show_in_listing" value="0">
            <input type="checkbox" name="show_in_listing" value="1" id="show_in_listing" {{ old('show_in_listing', isset($promotion) ? $promotion->show_in_listing : true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
        </div>
        <div class="ml-3 text-sm">
            <label for="show_in_listing" class="font-semibold text-gray-800 cursor-pointer">
                Tampilkan di daftar "Promo Menarik Tersedia" pada checkout
            </label>
            <p class="text-xs text-gray-600 mt-0.5 leading-relaxed">
                Jika dicentang, promo akan tampil di daftar pilihan halaman checkout publik ("Promo Menarik Tersedia"). Jika dinonaktifkan (disembunyikan), promo tidak akan muncul di daftar publik, namun <strong>pelanggan yang mengetikkan kodenya secara manual dengan benar tetap dapat menggunakannya</strong>.
            </p>
        </div>
    </div>
</div>
