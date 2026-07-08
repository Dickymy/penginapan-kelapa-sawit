<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Promo</label>
        <input type="text" name="code" value="{{ old('code', $promotion->code ?? '') }}" required maxlength="100" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm uppercase focus:ring-primary-500 focus:border-primary-500" placeholder="DISKON10">
        @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" name="name" value="{{ old('name', $promotion->name ?? '') }}" required maxlength="150" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Diskon 10%">
        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('description', $promotion->description ?? '') }}</textarea>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
        <select name="type" id="promo_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500"
                x-data x-on:change="$dispatch('promo-type-changed', { value: $el.value })">
            <option value="percentage" {{ old('type', ($promotion->type->value ?? '') ) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
            <option value="fixed" {{ old('type', ($promotion->type->value ?? '') ) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
        </select>
        @error('type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div x-data="{ type: '{{ old('type', ($promotion->type->value ?? 'percentage')) }}' }" x-on:promo-type-changed.window="type = $event.detail.value">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Nilai <span class="text-gray-400 font-normal" x-text="type === 'percentage' ? '(%)' : '(Rupiah)'"></span>
        </label>
        <input type="number" name="value" value="{{ old('value', $promotion->value ?? '') }}" required min="1" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500"
               :placeholder="type === 'percentage' ? '10' : '50000'">
        <p class="text-xs text-gray-400 mt-1" x-show="type === 'percentage'" x-cloak>Masukkan angka persen, misal: 10 untuk diskon 10%</p>
        <p class="text-xs text-gray-400 mt-1" x-show="type === 'fixed'" x-cloak>Masukkan jumlah Rupiah, misal: 50000 untuk diskon Rp50.000</p>
        @error('value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($promotion) ? $promotion->starts_at->format('Y-m-d\TH:i') : '') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        @error('starts_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Berakhir</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', isset($promotion) ? $promotion->ends_at->format('Y-m-d\TH:i') : '') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        @error('ends_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Booking (Rp)</label>
        <input type="number" name="minimum_booking_amount" value="{{ old('minimum_booking_amount', $promotion->minimum_booking_amount ?? 0) }}" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Maks. Diskon (Rp, opsional)</label>
        <input type="number" name="maximum_discount" value="{{ old('maximum_discount', $promotion->maximum_discount ?? '') }}" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
    </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Penggunaan (opsional)</label>
        <input type="number" name="usage_quota" value="{{ old('usage_quota', $promotion->usage_quota ?? '') }}" min="1" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Maks. per User (opsional)</label>
        <input type="number" name="max_usage_per_user" value="{{ old('max_usage_per_user', $promotion->max_usage_per_user ?? '') }}" min="1" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
    </div>
</div>

<div class="flex items-center">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $promotion->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
    <label for="is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
</div>
