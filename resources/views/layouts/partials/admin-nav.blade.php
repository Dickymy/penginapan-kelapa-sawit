{{-- Admin Navigation Groups --}}

{{-- RINGKASAN --}}
<div>
    <p class="px-3 mb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Ringkasan</p>
    <a href="{{ route('admin.dashboard') }}"
       class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
    </a>

    {{-- Reviews --}}
    <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.reviews.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        Ulasan
    </a>

    {{-- Contact Messages --}}
    <a href="{{ route('admin.contact-messages.index') }}" class="flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.contact-messages.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
        <div class="flex items-center gap-2.5">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Pesan Kontak
        </div>
        @php $unreadContactsCount = \App\Models\ContactMessage::unread()->count(); @endphp
        @if($unreadContactsCount > 0)
            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.contact-messages.*') ? 'bg-primary-700 text-white' : 'bg-primary-100 text-primary-700' }}">
                {{ $unreadContactsCount }}
            </span>
        @endif
    </a>
</div>

{{-- OPERASIONAL --}}
<div>
    <p class="px-3 mb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Operasional</p>
    <div class="space-y-0.5">
        <a href="{{ route('admin.calendar.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.calendar.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Kalender
        </a>
        <a href="{{ route('admin.bookings.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.bookings.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Reservasi
        </a>
        <a href="{{ route('admin.booking-changes.index') }}" class="flex items-center justify-between gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.booking-changes.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Perubahan Booking
            </div>
            @php $pendingChangesCount = \App\Models\BookingChangeRequest::where('status', 'pending')->count(); @endphp
            @if($pendingChangesCount > 0)
                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.booking-changes.*') ? 'bg-primary-700 text-white' : 'bg-primary-100 text-primary-700' }}">
                    {{ $pendingChangesCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('admin.room-blocks.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.room-blocks.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            Blokir Kamar
        </a>
    </div>
</div>

{{-- PROPERTI --}}
<div>
    <p class="px-3 mb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Properti</p>
    <div class="space-y-0.5">
        <a href="{{ route('admin.room-types.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.room-types.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Tipe Kamar
        </a>
        <a href="{{ route('admin.rooms.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.rooms.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            Kamar Fisik
        </a>
        <a href="{{ route('admin.facilities.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.facilities.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            Fasilitas
        </a>
        <a href="{{ route('admin.addons.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.addons.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Layanan Tambahan
        </a>
        {{-- Nearby Places --}}
        <a href="{{ route('admin.nearby-places.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.nearby-places.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Lokasi Sekitar
        </a>

        {{-- Galleries --}}
        <a href="{{ route('admin.galleries.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.galleries.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Galeri Foto
        </a>
    </div>
</div>

{{-- PELANGGAN & PEMASARAN --}}
<div>
    <p class="px-3 mb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Pelanggan</p>
    <div class="space-y-0.5">
        <a href="{{ route('admin.promotions.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.promotions.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Promo
        </a>
        <a href="{{ route('admin.rate-overrides.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.rate-overrides.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Harga Dinamis
        </a>
        <a href="{{ route('admin.loyalty.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.loyalty.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Poin Loyalitas
        </a>
    </div>
</div>

{{-- KEUANGAN --}}
<div>
    <p class="px-3 mb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Keuangan</p>
    <div class="space-y-0.5">
        <a href="{{ route('admin.expenses.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.expenses.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Pengeluaran
        </a>
        <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.reports.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="flex-1 text-left">Laporan</span>
                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak class="ml-7 mt-0.5 space-y-0.5">
                <a href="{{ route('admin.reports.revenue') }}" class="block px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('admin.reports.revenue') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-500 hover:bg-gray-100' }}">Pendapatan</a>
                <a href="{{ route('admin.reports.occupancy') }}" class="block px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('admin.reports.occupancy') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-500 hover:bg-gray-100' }}">Okupansi</a>
                <a href="{{ route('admin.reports.profit') }}" class="block px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('admin.reports.profit') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-500 hover:bg-gray-100' }}">Laba Rugi</a>
                <a href="{{ route('admin.reports.sources') }}" class="block px-3 py-1.5 rounded-lg text-xs {{ request()->routeIs('admin.reports.sources') ? 'text-primary-700 font-medium bg-primary-50' : 'text-gray-500 hover:bg-gray-100' }}">Sumber Booking</a>
            </div>
        </div>
    </div>
</div>

{{-- KONTEN & SISTEM --}}
<div>
    <p class="px-3 mb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Sistem</p>
    <div class="space-y-0.5">
        <a href="{{ route('admin.faqs.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.faqs.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            FAQ
        </a>
        <a href="{{ route('admin.policies.index') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.policies.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Kebijakan
        </a>
        <a href="{{ route('admin.settings.edit', 'general') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
    </div>
</div>
