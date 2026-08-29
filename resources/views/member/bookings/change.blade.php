@extends('layouts.member')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('member.bookings.show', $booking) }}" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
            <svg class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-900">Ajukan Perubahan Booking</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <h2 class="font-semibold text-slate-800">Booking Saat Ini ({{ $booking->booking_code }})</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                <div>
                    <span class="text-slate-500 block">Check-in</span>
                    <span class="font-medium">{{ $booking->check_in->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Check-out</span>
                    <span class="font-medium">{{ $booking->check_out->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Tipe Kamar</span>
                    <span class="font-medium">{{ $booking->room->roomType->name }}</span>
                </div>
            </div>
        </div>

        <form id="change-form" x-data="changePreview()" x-init="$nextTick(() => fetchPreview())" action="{{ route('member.booking-changes.store', $booking) }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Perubahan</label>
                <select name="type" class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="reschedule" {{ old('type') == 'reschedule' ? 'selected' : '' }}>Reschedule (Ubah Tanggal)</option>
                    <option value="room_change" {{ old('type') == 'room_change' ? 'selected' : '' }}>Ubah Tipe Kamar</option>
                    <option value="guest_update" {{ old('type') == 'guest_update' ? 'selected' : '' }}>Ubah Jumlah Tamu</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Check-in Baru</label>
                    <input type="date" name="check_in" x-model="checkInDate" @change="handleCheckInChange()" 
                        min="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                    @error('check_in')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Check-out Baru</label>
                    <input type="date" name="check_out" x-model="checkOutDate" @change="fetchPreview()" 
                        :min="getMinCheckOut()"
                        class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                    @error('check_out')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Kamar Baru</label>
                    <select name="room_type_id" x-ref="roomSelect" @change="fetchPreview()" class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                        @foreach($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" {{ old('room_type_id', $booking->room->room_type_id) == $roomType->id ? 'selected' : '' }}>
                                {{ $roomType->name }} - Rp{{ number_format($roomType->base_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Tamu Baru</label>
                    <input type="number" name="guest_count" x-model="guestCount" @change="fetchPreview()" value="{{ old('guest_count', $booking->guest_count) }}" 
                        min="1" class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500" required>
                    @error('guest_count')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Live Preview Section -->
            <div class="mt-6 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm relative">
                <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center">
                    <svg class="animate-spin h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Rincian Biaya Perubahan
                    </h3>
                </div>

                <div class="p-5">
                    <div x-show="error" class="bg-red-50 text-red-700 p-3 rounded-lg text-sm mb-4" x-text="error" x-cloak></div>

                    <div x-show="preview && preview.is_available" class="space-y-3 text-sm text-slate-600" x-cloak>
                        <div class="flex justify-between">
                            <span>Harga Kamar (<span x-text="preview?.nights"></span> malam)</span>
                            <span x-text="formatRupiah(preview?.room_subtotal)"></span>
                        </div>
                        
                        <div class="flex justify-between" x-show="preview?.addon_total > 0">
                            <span>Layanan Tambahan (Terbawa otomatis)</span>
                            <span x-text="formatRupiah(preview?.addon_total)"></span>
                        </div>
                        <template x-for="addon in (preview && preview.addon_details ? preview.addon_details : [])" :key="addon.id || addon.name">
                            <div class="flex justify-between text-xs text-slate-500 pl-4">
                                <span x-text="addon.name + ' (x' + addon.quantity + ')'"></span>
                                <span x-text="formatRupiah(addon.subtotal)"></span>
                            </div>
                        </template>

                        <div class="flex justify-between text-red-600" x-show="preview?.promotion_discount > 0">
                            <span>Diskon Promo (Terbawa otomatis)</span>
                            <span x-text="'- ' + formatRupiah(preview?.promotion_discount)"></span>
                        </div>
                        <div class="flex justify-between text-amber-600" x-show="preview?.points_discount > 0">
                            <span>Diskon Poin (Terbawa otomatis)</span>
                            <span x-text="'- ' + formatRupiah(preview?.points_discount)"></span>
                        </div>

                        <div class="pt-3 border-t border-slate-100 mt-3 border-dashed">
                            <div class="flex justify-between font-medium">
                                <span>Total Tagihan Baru</span>
                                <span x-text="formatRupiah(preview?.new_total)"></span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Total Tagihan Lama</span>
                                <span x-text="'- ' + formatRupiah(preview?.old_total)"></span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-200 mt-3 flex justify-between items-center bg-slate-50 -mx-5 px-5 py-3 -mb-5">
                            <span class="font-bold text-slate-900">Selisih Akhir</span>
                            <div class="text-right">
                                <span x-show="preview?.price_difference > 0" class="text-lg font-bold text-amber-600" x-text="'+ ' + formatRupiah(preview?.price_difference)"></span>
                                <span x-show="preview?.price_difference < 0" class="text-lg font-bold text-emerald-600" x-text="'Refund: ' + formatRupiah(Math.abs(preview?.price_difference))"></span>
                                <span x-show="preview?.price_difference === 0" class="text-lg font-bold text-slate-600">Rp0 (Tidak ada selisih)</span>
                                <p x-show="preview?.price_difference > 0" class="text-xs text-slate-500 mt-0.5">Kurang bayar</p>
                                <p x-show="preview?.price_difference < 0" class="text-xs text-slate-500 mt-0.5">Akan dikembalikan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-sm mt-6 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="space-y-2">
                    <p><strong>Penting:</strong></p>
                    <ul class="list-disc ml-4 space-y-1">
                        <li>Pengajuan perubahan akan ditinjau oleh Admin (proses manual).</li>
                        <li>Layanan Tambahan yang Anda beli sebelumnya akan otomatis terbawa ke tagihan baru.</li>
                        <li>Jika selisih akhir menyatakan <strong>Kurang bayar</strong>, Anda harus mentransfer selisih biayanya.</li>
                        <li>Jika selisih akhir menyatakan <strong>Refund</strong>, sistem akan mengembalikan kelebihan dana ke rekening Anda.</li>
                    </ul>
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t pt-6 mt-6">
                <a href="{{ route('member.bookings.show', $booking) }}" class="px-6 py-2 border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
                <button type="button" @click="showConfirmModal = true" :disabled="loading || (preview && !preview.is_available)" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    Kirim Pengajuan
                </button>
            </div>

            <!-- Confirmation Modal -->
            <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-cloak>
                <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4 overflow-hidden" @click.away="showConfirmModal = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-xl font-bold text-slate-900">Konfirmasi Pengajuan</h3>
                        <p class="text-sm text-slate-500 mt-1">Pastikan rincian perubahan booking Anda sudah benar.</p>
                    </div>
                    
                    <div class="p-6 space-y-4 text-sm" x-show="preview">
                        <div class="grid grid-cols-2 gap-y-3">
                            <div class="text-slate-500">Check-in Baru</div>
                            <div class="font-medium text-slate-900" x-text="formatDate(checkInDate)"></div>
                            
                            <div class="text-slate-500">Check-out Baru</div>
                            <div class="font-medium text-slate-900" x-text="formatDate(checkOutDate)"></div>

                            <div class="text-slate-500">Tipe Kamar</div>
                            <div class="font-medium text-slate-900" x-text="roomTypeName"></div>
                            
                            <div class="text-slate-500">Jumlah Tamu</div>
                            <div class="font-medium text-slate-900" x-text="guestCount + ' orang'"></div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 mt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Selisih Biaya</span>
                                <div class="text-right">
                                    <span x-show="preview?.price_difference > 0" class="font-bold text-amber-600 text-base" x-text="'+ ' + formatRupiah(preview?.price_difference)"></span>
                                    <span x-show="preview?.price_difference < 0" class="font-bold text-emerald-600 text-base" x-text="'Refund: ' + formatRupiah(Math.abs(preview?.price_difference))"></span>
                                    <span x-show="preview?.price_difference === 0" class="font-bold text-slate-600 text-base">Rp0 (Tidak ada selisih)</span>
                                </div>
                            </div>
                            <p x-show="preview?.price_difference > 0" class="text-xs text-amber-600 mt-1 text-right">Anda harus melakukan pembayaran selisih.</p>
                            <p x-show="preview?.price_difference < 0" class="text-xs text-emerald-600 mt-1 text-right">Sistem akan me-refund kelebihan dana.</p>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-slate-50 flex justify-end gap-3">
                        <button type="button" @click="showConfirmModal = false" class="px-5 py-2 border border-slate-300 text-slate-700 rounded-xl hover:bg-white transition-colors font-medium">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors font-medium">Ya, Ajukan Perubahan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changePreview() {
    return {
        showConfirmModal: false,
        checkInDate: '{{ old('check_in', $booking->check_in->format('Y-m-d')) }}',
        checkOutDate: '{{ old('check_out', $booking->check_out->format('Y-m-d')) }}',
        guestCount: '{{ old('guest_count', $booking->guest_count) }}',
        roomTypeName: '',
        loading: false,
        preview: null,
        error: null,
        handleCheckInChange() {
            if (!this.checkInDate) return;
            let ci = new Date(this.checkInDate + 'T00:00:00');
            let co = this.checkOutDate ? new Date(this.checkOutDate + 'T00:00:00') : null;
            if (!co || ci >= co) {
                let newCo = new Date(ci);
                newCo.setDate(newCo.getDate() + 1);
                let y = newCo.getFullYear();
                let m = String(newCo.getMonth() + 1).padStart(2, '0');
                let d = String(newCo.getDate()).padStart(2, '0');
                this.checkOutDate = `${y}-${m}-${d}`;
            }
            this.fetchPreview();
        },
        getMinCheckOut() {
            if (!this.checkInDate) return '{{ now()->addDay()->format('Y-m-d') }}';
            let ci = new Date(this.checkInDate + 'T00:00:00');
            ci.setDate(ci.getDate() + 1);
            let y = ci.getFullYear();
            let m = String(ci.getMonth() + 1).padStart(2, '0');
            let d = String(ci.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        },
        fetchPreview() {
            let formElement = document.getElementById('change-form');
            if (!formElement) {
                this.error = 'Form element not found!';
                return;
            }
            if (this.$refs.roomSelect) {
                this.roomTypeName = this.$refs.roomSelect.options[this.$refs.roomSelect.selectedIndex].text.split('-')[0].trim();
            }
            
            let formData = new FormData(formElement);
            if(!formData.get('check_in') || !formData.get('check_out') || !formData.get('room_type_id') || !formData.get('guest_count')) {
                this.error = 'Mohon lengkapi semua field terlebih dahulu.';
                return;
            }
            
            this.loading = true;
            this.error = null;
            
            fetch("{{ route('member.booking-changes.calculate', $booking) }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if(!response.ok) {
                    if(response.status !== 422) {
                        throw new Error('HTTP Error: ' + response.status);
                    }
                }
                return response.json().then(data => ({ status: response.status, body: data }));
            })
            .then(res => {
                this.loading = false;
                if(res.status === 200) {
                    this.preview = res.body;
                    if(!this.preview.is_available) {
                        this.error = this.preview.message || 'Kamar tidak tersedia.';
                    }
                } else {
                    this.error = res.body.message || 'Terjadi kesalahan sistem.';
                    this.preview = null;
                }
            })
            .catch(err => {
                this.loading = false;
                this.error = 'Gagal menghitung selisih harga: ' + err.message;
            });
        },
        formatRupiah(number) {
            if(number === null || number === undefined || isNaN(number)) return 'Rp0';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        },
        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr + 'T00:00:00');
            return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(date);
        }
    };
}
</script>
@endpush
