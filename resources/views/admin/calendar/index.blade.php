@extends('layouts.admin')

@section('title', 'Kalender Ketersediaan - Admin')
@section('page-title', 'Kalender Ketersediaan')

@section('content')
<div x-data="calendarApp()" x-init="init()" class="space-y-6">

    {{-- Top Control Card --}}
    <div class="bg-white rounded-2xl shadow-xs border border-gray-200/90 p-4 sm:p-6 space-y-4">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            {{-- Title & Today Pill --}}
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Kalender Kamar</span>
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Pantau status keterisian, blokir kamar, dan reservasi secara real-time</p>
            </div>

            {{-- Mode Switcher (Agenda Harian / 7 Hari / Grid Bulanan) --}}
            <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl w-full sm:w-auto overflow-x-auto shrink-0">
                <button type="button" 
                        @click="setViewMode('agenda')"
                        :class="viewMode === 'agenda' ? 'bg-white text-primary-700 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Agenda Harian</span>
                    <span class="text-[10px] px-1 py-0.2 bg-primary-50 text-primary-700 rounded-full font-bold sm:hidden">Mudah</span>
                </button>
                <button type="button" 
                        @click="setViewMode('week')"
                        :class="viewMode === 'week' ? 'bg-white text-primary-700 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>7 Hari (Mingguan)</span>
                </button>
                <button type="button" 
                        @click="setViewMode('month')"
                        :class="viewMode === 'month' ? 'bg-white text-primary-700 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Grid Bulanan</span>
                </button>
            </div>
        </div>

        {{-- Sub-controls: Navigation & Filters --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pt-3 border-t border-gray-100">
            {{-- Navigation Dates --}}
            <div class="flex items-center gap-2">
                {{-- Agenda Harian Nav --}}
                <template x-if="viewMode === 'agenda'">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" @click="changeAgendaDate(-1)" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 transition" title="Hari Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" @click="setAgendaToday()" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-700 transition">
                            Hari Ini
                        </button>
                        <button type="button" @click="changeAgendaDate(1)" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 transition" title="Hari Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div class="relative ml-1">
                            <input type="date" x-model="agendaDateStr" @change="onAgendaDateChange()" class="text-xs font-bold text-gray-900 border border-gray-200 rounded-lg px-2.5 py-1.5 bg-white shadow-2xs cursor-pointer">
                        </div>
                    </div>
                </template>

                {{-- Week Nav --}}
                <template x-if="viewMode === 'week'">
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="changeWeek(-1)" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 transition" title="Minggu Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" @click="setWeekToday()" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-700 transition">
                            Minggu Ini
                        </button>
                        <button type="button" @click="changeWeek(1)" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 transition" title="Minggu Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <span class="text-xs font-bold text-gray-800 ml-2" x-text="weekRangeString"></span>
                    </div>
                </template>

                {{-- Month Nav --}}
                <template x-if="viewMode === 'month'">
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="changeMonth(-1)" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 transition" title="Bulan Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" @click="setMonthToday()" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-700 transition">
                            Bulan Ini
                        </button>
                        <button type="button" @click="changeMonth(1)" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 transition" title="Bulan Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <span class="text-sm font-bold text-gray-900 ml-2" x-text="monthYearString"></span>
                        <button type="button" @click="scrollToTodayInGrid()" class="text-xs text-primary-600 hover:text-primary-800 font-semibold underline ml-2 hidden md:inline cursor-pointer">
                            Lompat ke Hari Ini &rarr;
                        </button>
                    </div>
                </template>
            </div>

            {{-- Room Type Filter & Quick Action Buttons --}}
            <div class="flex items-center gap-2 flex-wrap">
                <select x-model="selectedRoomTypeId" class="text-xs border-gray-200 rounded-lg py-1.5 pl-3 pr-8 bg-white font-medium text-gray-700 focus:ring-primary-500 focus:border-primary-500">
                    <option value="all">Semua Tipe Kamar</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>

                <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white rounded-lg text-xs font-bold shadow-2xs transition">
                    <span>+ Reservasi</span>
                </a>
                <a href="{{ route('admin.room-blocks.create') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 active:scale-95 text-gray-700 rounded-lg text-xs font-bold transition">
                    <span>+ Blokir</span>
                </a>
            </div>
        </div>

        {{-- Color Legends --}}
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-3 border-t border-gray-100 text-xs">
            <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Keterangan:</span>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="text-gray-700 font-medium">Tersedia (Kosong)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span><span class="text-gray-700 font-medium">Checked-in</span></div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-600"></span><span class="text-gray-700 font-medium">Confirmed</span></div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span><span class="text-gray-700 font-medium">Pending Bayar</span></div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span><span class="text-gray-700 font-medium">Checked-out</span></div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-gray-300 diagonal-stripes"></span><span class="text-gray-700 font-medium">Diblokir</span></div>
        </div>
    </div>

    {{-- Loading Spinner --}}
    <div x-show="loading" class="bg-white rounded-2xl border border-gray-200 p-12 flex flex-col items-center justify-center min-h-[350px]">
        <svg class="animate-spin h-8 w-8 text-primary-600 mb-3" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-xs text-gray-500 font-medium">Memuat data ketersediaan kamar...</p>
    </div>

    {{-- VIEW 1: AGENDA HARIAN (Mobile-First, Card Based, ZERO horizontal scrolling) --}}
    <div x-show="!loading && viewMode === 'agenda'" x-cloak class="space-y-4">
        {{-- Daily Summary Banner --}}
        <div class="bg-gradient-to-r from-primary-900 to-primary-800 rounded-2xl p-4 sm:p-5 text-white shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold text-primary-200 uppercase tracking-wider">Status Harian Kamar</p>
                <h3 class="text-lg sm:text-xl font-black mt-0.5" x-text="formatAgendaDisplayDate(agendaDateStr)"></h3>
            </div>
            <div class="flex items-center gap-2 flex-wrap text-xs font-semibold">
                <span class="px-2.5 py-1 rounded-lg bg-white/10 backdrop-blur-xs text-white">
                    Total: <strong x-text="filteredRooms.length"></strong> Kamar
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/80 text-white">
                    Tersedia: <strong x-text="getAgendaAvailableCount()"></strong>
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-blue-500/80 text-white">
                    Terisi: <strong x-text="getAgendaOccupiedCount()"></strong>
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-gray-500/80 text-white">
                    Blokir: <strong x-text="getAgendaBlockedCount()"></strong>
                </span>
            </div>
        </div>

        {{-- Vertical List of Room Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="room in filteredRooms" :key="'agenda-r-' + room.id">
                <div class="bg-white rounded-2xl border p-4 shadow-xs transition hover:shadow-md flex flex-col justify-between"
                     :class="getAgendaCardBorderClass(room.id, agendaDateStr)">
                    
                    {{-- Card Header: Room Name & Type --}}
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="text-base font-extrabold text-gray-900 leading-tight" x-text="room.name"></h4>
                                <span class="text-xs text-gray-500 font-medium" x-text="room.room_type"></span>
                            </div>
                            <div>
                                <span class="inline-flex text-[11px] font-bold px-2.5 py-1 rounded-full"
                                      :class="getAgendaBadgeClass(room.id, agendaDateStr)"
                                      x-text="getAgendaBadgeText(room.id, agendaDateStr)">
                                </span>
                            </div>
                        </div>

                        {{-- Card Body: Status Content --}}
                        <div class="mt-3.5 pt-3 border-t border-gray-100">
                            {{-- Case A: Booking Exists --}}
                            <template x-if="getRoomBookingOnDate(room.id, agendaDateStr)">
                                <div class="space-y-2">
                                    <div class="bg-gray-50/90 rounded-xl p-3 border border-gray-100 text-xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500">Tamu:</span>
                                            <strong class="text-gray-900 font-bold" x-text="getRoomBookingOnDate(room.id, agendaDateStr).guest_name"></strong>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500">Kode Booking:</span>
                                            <span class="font-mono text-gray-700 font-semibold" x-text="getRoomBookingOnDate(room.id, agendaDateStr).booking_code"></span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500">Periode:</span>
                                            <span class="text-gray-800" x-text="formatDateShort(getRoomBookingOnDate(room.id, agendaDateStr).check_in) + ' - ' + formatDateShort(getRoomBookingOnDate(room.id, agendaDateStr).check_out) + ' (' + getRoomBookingOnDate(room.id, agendaDateStr).nights + ' malam)'"></span>
                                        </div>
                                    </div>
                                    <div class="pt-1 flex items-center justify-end">
                                        <a :href="'/admin/bookings/' + getRoomBookingOnDate(room.id, agendaDateStr).id"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-primary-600 hover:text-primary-800">
                                            <span>Lihat Detail Reservasi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </template>

                            {{-- Case B: Room Blocked --}}
                            <template x-if="!getRoomBookingOnDate(room.id, agendaDateStr) && getRoomBlockOnDate(room.id, agendaDateStr)">
                                <div class="bg-gray-100/80 diagonal-stripes rounded-xl p-3 border border-gray-200 text-xs space-y-1">
                                    <div class="flex items-center justify-between bg-white/90 p-1.5 rounded-lg">
                                        <span class="text-gray-500">Alasan Blokir:</span>
                                        <strong class="text-gray-800" x-text="getRoomBlockOnDate(room.id, agendaDateStr).reason"></strong>
                                    </div>
                                    <div class="flex items-center justify-between bg-white/90 p-1.5 rounded-lg text-[11px]">
                                        <span class="text-gray-500">Sampai:</span>
                                        <span class="text-gray-700" x-text="getRoomBlockOnDate(room.id, agendaDateStr).end_date ? formatDateShort(getRoomBlockOnDate(room.id, agendaDateStr).end_date) : 'Seterusnya'"></span>
                                    </div>
                                    <div class="pt-1 text-right">
                                        <a href="{{ route('admin.room-blocks.index') }}" class="text-[11px] font-bold text-gray-700 hover:underline">
                                            Kelola Blokir Kamar &rarr;
                                        </a>
                                    </div>
                                </div>
                            </template>

                            {{-- Case C: Available / Kosong --}}
                            <template x-if="!getRoomBookingOnDate(room.id, agendaDateStr) && !getRoomBlockOnDate(room.id, agendaDateStr)">
                                <div class="space-y-3">
                                    <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100 text-xs text-emerald-800 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Kamar siap dihuni &amp; belum ada reservasi untuk tanggal ini.</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a :href="'/admin/bookings/create?room_id=' + room.id + '&check_in=' + agendaDateStr"
                                           class="flex-1 inline-flex items-center justify-center gap-1 py-2 px-3 bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-bold rounded-xl transition">
                                            <span>+ Reservasi</span>
                                        </a>
                                        <a :href="'/admin/room-blocks/create?room_id=' + room.id + '&start_date=' + agendaDateStr"
                                           class="inline-flex items-center justify-center gap-1 py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-xl transition">
                                            <span>Blokir</span>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- VIEW 2: MINGGUAN (7 Hari Rolling Window, Compact on Tablet/Mobile) --}}
    <div x-show="!loading && viewMode === 'week'" x-cloak class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <div class="min-w-[650px]">
                {{-- Header 7 Days --}}
                <div class="flex border-b border-gray-200 bg-gray-50">
                    <div class="w-40 sm:w-48 p-3.5 font-bold text-gray-700 text-xs uppercase tracking-wider shrink-0 border-r border-gray-200">
                        Kamar
                    </div>
                    <template x-for="day in weekDays" :key="'w-day-' + day.date">
                        <div class="flex-1 p-2 text-center border-r border-gray-200 last:border-r-0"
                             :class="isToday(day.date) ? 'bg-primary-50 text-primary-800' : (isWeekend(day.date) ? 'bg-gray-100/70 text-gray-600' : 'text-gray-700')">
                            <div class="text-[11px] font-medium" x-text="day.dayName"></div>
                            <div class="text-sm sm:text-base font-extrabold mt-0.5" x-text="day.dayNumber"></div>
                        </div>
                    </template>
                </div>

                {{-- Room Rows in Week View --}}
                <div class="divide-y divide-gray-100">
                    <template x-for="room in filteredRooms" :key="'week-r-' + room.id">
                        <div class="flex items-center hover:bg-gray-50/50 transition">
                            <div class="w-40 sm:w-48 p-3 shrink-0 border-r border-gray-200 bg-white">
                                <div class="font-bold text-gray-900 text-xs sm:text-sm truncate" x-text="room.name"></div>
                                <div class="text-[11px] text-gray-500 truncate" x-text="room.room_type"></div>
                            </div>
                            <template x-for="day in weekDays" :key="'week-cell-' + room.id + '-' + day.date">
                                <div class="flex-1 h-14 p-1 border-r border-gray-100 last:border-r-0 flex items-center justify-center relative"
                                     :class="isToday(day.date) ? 'bg-primary-50/20' : (isWeekend(day.date) ? 'bg-gray-50/50' : '')">
                                    
                                    {{-- If Booking on Day --}}
                                    <template x-if="getRoomBookingOnDate(room.id, day.date)">
                                        <a :href="'/admin/bookings/' + getRoomBookingOnDate(room.id, day.date).id"
                                           class="w-full h-10 rounded-lg p-1.5 text-[10px] font-bold text-white shadow-2xs truncate flex flex-col justify-center transition hover:opacity-90"
                                           :class="getBookingColorClass(getRoomBookingOnDate(room.id, day.date).status)"
                                           :title="getRoomBookingOnDate(room.id, day.date).guest_name + ' (' + getRoomBookingOnDate(room.id, day.date).status_label + ')'">
                                            <span class="truncate leading-tight" x-text="getRoomBookingOnDate(room.id, day.date).guest_name"></span>
                                        </a>
                                    </template>

                                    {{-- If Block on Day --}}
                                    <template x-if="!getRoomBookingOnDate(room.id, day.date) && getRoomBlockOnDate(room.id, day.date)">
                                        <div class="w-full h-10 rounded-lg p-1 text-[10px] text-gray-700 bg-gray-200 diagonal-stripes border border-gray-300 truncate flex items-center justify-center font-bold"
                                             :title="getRoomBlockOnDate(room.id, day.date).reason">
                                            <span class="truncate bg-white/80 px-1 rounded">Blokir</span>
                                        </div>
                                    </template>

                                    {{-- If Free on Day --}}
                                    <template x-if="!getRoomBookingOnDate(room.id, day.date) && !getRoomBlockOnDate(room.id, day.date)">
                                        <span class="w-2 h-2 rounded-full bg-emerald-300/60" title="Tersedia"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- VIEW 3: GRID BULANAN (Full Timeline Matrix, with Auto-Scroll to Today) --}}
    <div x-show="!loading && viewMode === 'month'" x-cloak class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
        <div id="grid-container" class="overflow-x-auto overflow-y-auto custom-scrollbar relative max-h-[70vh]">
            <div class="min-w-max">
                {{-- Header Row --}}
                <div class="flex border-b border-gray-200 sticky top-0 bg-gray-50 z-20">
                    <div class="w-36 sm:w-48 flex-shrink-0 border-r border-gray-200 p-3 font-bold text-xs uppercase tracking-wider text-gray-700 bg-gray-50 sticky left-0 z-30 flex items-center">
                        Kamar
                    </div>
                    <template x-for="day in daysInMonth" :key="day.date">
                        <div :id="'grid-day-' + day.dayNumber"
                             class="w-16 flex-shrink-0 border-r border-gray-200 p-2 text-center text-xs font-medium"
                             :class="{ 'bg-primary-50 text-primary-800 font-bold': isToday(day.date), 'bg-gray-100/70 text-gray-500': isWeekend(day.date), 'text-gray-600': !isToday(day.date) && !isWeekend(day.date) }">
                            <div class="text-[11px]" x-text="day.dayName"></div>
                            <div class="text-sm font-extrabold mt-0.5" x-text="day.dayNumber"></div>
                        </div>
                    </template>
                </div>

                {{-- Room Rows --}}
                <div class="divide-y divide-gray-100">
                    <template x-for="room in filteredRooms" :key="room.id">
                        <div class="flex hover:bg-gray-50/50 transition relative group h-14">
                            {{-- Room Info Column --}}
                            <div class="w-36 sm:w-48 flex-shrink-0 border-r border-gray-200 p-2.5 bg-white group-hover:bg-gray-50 sticky left-0 z-10 flex flex-col justify-center">
                                <div class="font-bold text-gray-900 text-xs sm:text-sm truncate" x-text="room.name" :title="room.name"></div>
                                <div class="text-[11px] text-gray-500 truncate" x-text="room.room_type" :title="room.room_type"></div>
                            </div>
                            
                            {{-- Days Grid & Absolute Bars --}}
                            <div class="flex relative flex-1">
                                <template x-for="day in daysInMonth" :key="day.date">
                                    <div class="w-16 flex-shrink-0 border-r border-gray-100"
                                         :class="{ 'bg-primary-50/30': isToday(day.date), 'bg-gray-50/40': isWeekend(day.date) }">
                                    </div>
                                </template>

                                {{-- Render Bookings Bars --}}
                                <template x-for="booking in getRoomBookings(room.id)" :key="'b-'+booking.id">
                                    <a :href="'/admin/bookings/' + booking.id" 
                                       class="absolute h-10 top-2 rounded-lg shadow-xs border border-white/20 text-xs font-bold text-white px-2.5 py-1 overflow-hidden cursor-pointer hover:opacity-90 transition transform hover:scale-[1.01] hover:z-20 truncate flex items-center"
                                       :class="getBookingColorClass(booking.status)"
                                       :style="getBookingStyle(booking)"
                                       :title="`${booking.guest_name}\n${booking.check_in} s/d ${booking.check_out}\nStatus: ${booking.status_label}`">
                                        <span x-text="booking.guest_name" class="truncate"></span>
                                    </a>
                                </template>

                                {{-- Render Room Blocks Bars --}}
                                <template x-for="block in getRoomBlocks(room.id)" :key="'rb-'+block.id">
                                    <div class="absolute h-10 top-2 rounded-lg border border-gray-300 bg-gray-200 opacity-85 flex items-center justify-center text-xs text-gray-700 font-bold overflow-hidden cursor-help diagonal-stripes shadow-2xs"
                                         :style="getBlockStyle(block)"
                                         :title="`Diblokir: ${block.reason}\n${block.start_date} s/d ${block.end_date ? block.end_date : 'Seterusnya'}`">
                                        <span class="bg-white/90 px-1.5 py-0.5 rounded truncate text-gray-800 text-[11px]" x-text="block.reason"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.diagonal-stripes {
    background-image: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 8px,
        rgba(0,0,0,0.06) 8px,
        rgba(0,0,0,0.06) 16px
    );
}
</style>

@push('scripts')
<script>
(() => {
    const parseLocalDate = (str) => {
        if (!str) return null;
        const parts = str.split('T')[0].split('-');
        return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    };

    const formatDateStr = (date) => {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    };

    const initCalendarApp = () => {
        Alpine.data('calendarApp', () => ({
            loading: true,
            viewMode: window.innerWidth < 768 ? 'agenda' : 'month',
            currentDate: new Date(),
            selectedRoomTypeId: 'all',
            
            // Agenda Harian state
            agendaDateStr: formatDateStr(new Date()),
            
            // Week View state
            weekStartDate: (() => {
                const now = new Date();
                const day = now.getDay();
                const diff = now.getDate() - day + (day === 0 ? -6 : 1); // Monday start
                return new Date(now.setDate(diff));
            })(),
            
            rooms: [],
            bookings: [],
            roomBlocks: [],

            init() {
                this.fetchData();
            },

            setViewMode(mode) {
                this.viewMode = mode;
                if (mode === 'month') {
                    this.$nextTick(() => {
                        this.scrollToTodayInGrid();
                    });
                }
            },

            get filteredRooms() {
                if (this.selectedRoomTypeId === 'all') {
                    return this.rooms;
                }
                return this.rooms.filter(r => String(r.room_type_id) === String(this.selectedRoomTypeId));
            },

            get monthYearString() {
                return this.currentDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            },

            get daysInMonth() {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();
                const daysCount = new Date(year, month + 1, 0).getDate();
                
                const days = [];
                for (let i = 1; i <= daysCount; i++) {
                    const date = new Date(year, month, i);
                    days.push({
                        date: formatDateStr(date),
                        dayNumber: i,
                        dayName: date.toLocaleDateString('id-ID', { weekday: 'short' }),
                    });
                }
                return days;
            },

            get weekDays() {
                const days = [];
                for (let i = 0; i < 7; i++) {
                    const d = new Date(this.weekStartDate);
                    d.setDate(d.getDate() + i);
                    days.push({
                        date: formatDateStr(d),
                        dayNumber: d.getDate(),
                        dayName: d.toLocaleDateString('id-ID', { weekday: 'short' }),
                    });
                }
                return days;
            },

            get weekRangeString() {
                if (this.weekDays.length < 7) return '';
                const start = this.weekDays[0].date;
                const end = this.weekDays[6].date;
                return `${this.formatDateShort(start)} - ${this.formatDateShort(end)}`;
            },

            // Navigation Helpers
            changeMonth(delta) {
                const newDate = new Date(this.currentDate);
                newDate.setDate(1);
                newDate.setMonth(newDate.getMonth() + delta);
                this.currentDate = newDate;
                this.fetchData();
            },

            setMonthToday() {
                this.currentDate = new Date();
                this.fetchData();
                this.$nextTick(() => this.scrollToTodayInGrid());
            },

            changeWeek(delta) {
                const newD = new Date(this.weekStartDate);
                newD.setDate(newD.getDate() + (delta * 7));
                this.weekStartDate = newD;
                // If week is in different month, fetch month data
                this.fetchData();
            },

            setWeekToday() {
                const now = new Date();
                const day = now.getDay();
                const diff = now.getDate() - day + (day === 0 ? -6 : 1);
                this.weekStartDate = new Date(now.setDate(diff));
                this.fetchData();
            },

            changeAgendaDate(delta) {
                const current = parseLocalDate(this.agendaDateStr) || new Date();
                current.setDate(current.getDate() + delta);
                this.agendaDateStr = formatDateStr(current);
            },

            setAgendaToday() {
                this.agendaDateStr = formatDateStr(new Date());
            },

            onAgendaDateChange() {
                // date updated via input
            },

            scrollToTodayInGrid() {
                const today = new Date();
                if (today.getMonth() === this.currentDate.getMonth() && today.getFullYear() === this.currentDate.getFullYear()) {
                    const el = document.getElementById('grid-day-' + today.getDate());
                    const container = document.getElementById('grid-container');
                    if (el && container) {
                        container.scrollTo({
                            left: Math.max(0, el.offsetLeft - 160),
                            behavior: 'smooth'
                        });
                    }
                }
            },

            // Check if day is today / weekend
            isToday(dateString) {
                return dateString === formatDateStr(new Date());
            },

            isWeekend(dateString) {
                const d = parseLocalDate(dateString);
                return d ? (d.getDay() === 0 || d.getDay() === 6) : false;
            },

            formatDateShort(dateStr) {
                if (!dateStr) return '';
                const d = parseLocalDate(dateStr);
                return d ? d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : dateStr;
            },

            formatAgendaDisplayDate(dateStr) {
                const d = parseLocalDate(dateStr) || new Date();
                return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            },

            async fetchData() {
                this.loading = true;
                try {
                    // Fetch spanning 3 months around currentDate to cover week / agenda smooth navigation
                    const y = this.currentDate.getFullYear();
                    const m = this.currentDate.getMonth();
                    const start = formatDateStr(new Date(y, m - 1, 1));
                    const end = formatDateStr(new Date(y, m + 2, 0));

                    const response = await fetch(`/admin/calendar/data?start_date=${start}&end_date=${end}`);
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    
                    this.rooms = data.rooms || [];
                    this.bookings = data.bookings || [];
                    this.roomBlocks = data.room_blocks || [];
                } catch (err) {
                    console.error('Calendar data error:', err);
                } finally {
                    this.loading = false;
                }
            },

            // Day Status Helpers for Agenda & Week
            getRoomBookingOnDate(roomId, dateStr) {
                return this.bookings.find(b => {
                    return b.room_id === roomId && (b.check_in <= dateStr && b.check_out > dateStr);
                });
            },

            getRoomBlockOnDate(roomId, dateStr) {
                return this.roomBlocks.find(b => {
                    if (b.room_id !== roomId) return false;
                    if (b.start_date > dateStr) return false;
                    if (b.end_date && b.end_date < dateStr) return false;
                    return true;
                });
            },

            getAgendaBadgeClass(roomId, dateStr) {
                const b = this.getRoomBookingOnDate(roomId, dateStr);
                if (b) {
                    switch(b.status) {
                        case 'checked_in': return 'bg-blue-100 text-blue-800 border border-blue-200';
                        case 'confirmed': return 'bg-green-100 text-green-800 border border-green-200';
                        case 'pending_payment': return 'bg-pink-100 text-pink-800 border border-pink-200';
                        default: return 'bg-gray-100 text-gray-700';
                    }
                }
                const block = this.getRoomBlockOnDate(roomId, dateStr);
                if (block) {
                    return 'bg-gray-200 text-gray-800 border border-gray-300 diagonal-stripes';
                }
                return 'bg-emerald-100 text-emerald-800 border border-emerald-200';
            },

            getAgendaBadgeText(roomId, dateStr) {
                const b = this.getRoomBookingOnDate(roomId, dateStr);
                if (b) return b.status_label || 'Terisi';
                const block = this.getRoomBlockOnDate(roomId, dateStr);
                if (block) return 'Diblokir';
                return 'Tersedia';
            },

            getAgendaCardBorderClass(roomId, dateStr) {
                const b = this.getRoomBookingOnDate(roomId, dateStr);
                if (b) {
                    if (b.status === 'checked_in') return 'border-blue-300 ring-1 ring-blue-100';
                    if (b.status === 'confirmed') return 'border-green-300 ring-1 ring-green-100';
                    return 'border-pink-300';
                }
                if (this.getRoomBlockOnDate(roomId, dateStr)) {
                    return 'border-gray-300 bg-gray-50/30';
                }
                return 'border-emerald-200 hover:border-emerald-300';
            },

            getAgendaAvailableCount() {
                return this.filteredRooms.filter(r => !this.getRoomBookingOnDate(r.id, this.agendaDateStr) && !this.getRoomBlockOnDate(r.id, this.agendaDateStr)).length;
            },

            getAgendaOccupiedCount() {
                return this.filteredRooms.filter(r => !!this.getRoomBookingOnDate(r.id, this.agendaDateStr)).length;
            },

            getAgendaBlockedCount() {
                return this.filteredRooms.filter(r => !this.getRoomBookingOnDate(r.id, this.agendaDateStr) && !!this.getRoomBlockOnDate(r.id, this.agendaDateStr)).length;
            },

            // Grid Month Helpers
            getRoomBookings(roomId) {
                return this.bookings.filter(b => b.room_id === roomId);
            },

            getRoomBlocks(roomId) {
                return this.roomBlocks.filter(b => b.room_id === roomId);
            },

            getBookingColorClass(status) {
                switch(status) {
                    case 'pending_payment': return 'bg-pink-500 hover:bg-pink-600';
                    case 'confirmed': return 'bg-green-600 hover:bg-green-700';
                    case 'checked_in': return 'bg-blue-600 hover:bg-blue-700';
                    case 'checked_out': return 'bg-gray-400 hover:bg-gray-500';
                    default: return 'bg-gray-500';
                }
            },

            _calculatePositionStyle(startStr, endStr) {
                const monthDays = this.daysInMonth;
                if (monthDays.length === 0) return 'display: none;';
                
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();
                const gridStart = new Date(year, month, 1);
                const gridEnd = new Date(year, month + 1, 1);
                
                let itemStart = parseLocalDate(startStr);
                let itemEnd = parseLocalDate(endStr);
                
                if (!itemStart || !itemEnd) return 'display: none;';
                
                if (itemEnd <= gridStart || itemStart >= gridEnd) {
                    return 'display: none;';
                }
                
                if (itemStart < gridStart) itemStart = gridStart;
                if (itemEnd > gridEnd) itemEnd = gridEnd;
                
                const CELL_WIDTH = 64;
                const msPerDay = 1000 * 60 * 60 * 24;
                
                const startOffsetDays = Math.round((itemStart - gridStart) / msPerDay);
                const durationDays = Math.round((itemEnd - itemStart) / msPerDay);
                
                if (durationDays <= 0) return 'display: none;';
                
                const left = startOffsetDays * CELL_WIDTH;
                const width = durationDays * CELL_WIDTH;
                
                return `left: ${left}px; width: ${width}px;`;
            },

            getBookingStyle(booking) {
                return this._calculatePositionStyle(booking.check_in, booking.check_out);
            },

            getBlockStyle(block) {
                let endAdjusted;
                if (block.end_date) {
                    const endD = parseLocalDate(block.end_date);
                    endD.setDate(endD.getDate() + 1);
                    endAdjusted = formatDateStr(endD);
                } else {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();
                    const nextMonthFirst = new Date(year, month + 1, 1);
                    endAdjusted = formatDateStr(nextMonthFirst);
                }
                return this._calculatePositionStyle(block.start_date, endAdjusted);
            }
        }));
    };

    if (window.Alpine) {
        initCalendarApp();
    } else {
        document.addEventListener('alpine:init', initCalendarApp);
    }
})();
</script>
@endpush
@endsection
