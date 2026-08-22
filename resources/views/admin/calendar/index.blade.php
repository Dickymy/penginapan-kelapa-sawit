@extends('layouts.admin')

@section('title', 'Kalender Ketersediaan - Admin')
@section('page-title', 'Kalender Ketersediaan')

@section('content')
<div x-data="calendarApp()" x-init="fetchData()" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col min-h-[600px]">
    
    <!-- Controls -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
        <div class="flex items-center gap-2">
            <button @click="changeMonth(-1)" class="p-2 rounded-md hover:bg-gray-100 text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h2 class="text-xl font-bold text-gray-800 w-48 text-center" x-text="monthYearString"></h2>
            <button @click="changeMonth(1)" class="p-2 rounded-md hover:bg-gray-100 text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-pink-400"></div><span class="text-gray-600">Pending</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-green-500"></div><span class="text-gray-600">Confirmed</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-blue-500"></div><span class="text-gray-600">Checked-in</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-gray-400"></div><span class="text-gray-600">Checked-out</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-sm bg-gray-200 diagonal-stripes"></div><span class="text-gray-600">Diblokir</span></div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex-1 flex items-center justify-center">
        <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    <!-- Calendar Grid -->
    <div x-show="!loading" class="flex-1 overflow-x-auto overflow-y-auto border border-gray-200 rounded-lg relative" x-cloak>
        <div class="min-w-max">
            <!-- Header Row -->
            <div class="flex border-b border-gray-200 sticky top-0 bg-gray-50 z-20">
                <div class="w-48 flex-shrink-0 border-r border-gray-200 p-3 font-semibold text-gray-700 bg-gray-50 sticky left-0 z-30 flex items-center">
                    Kamar
                </div>
                <template x-for="day in daysInMonth" :key="day.date">
                    <div class="w-12 flex-shrink-0 border-r border-gray-200 p-2 text-center text-xs font-medium text-gray-500"
                         :class="{ 'bg-primary-50 text-primary-700': isToday(day.date), 'bg-gray-100': isWeekend(day.date) }">
                        <div x-text="day.dayName"></div>
                        <div class="text-base font-bold mt-0.5" x-text="day.dayNumber"></div>
                    </div>
                </template>
            </div>

            <!-- Room Rows -->
            <template x-for="room in rooms" :key="room.id">
                <div class="flex border-b border-gray-100 hover:bg-gray-50 transition relative group h-14">
                    <!-- Room Info -->
                    <div class="w-48 flex-shrink-0 border-r border-gray-200 p-2 bg-white group-hover:bg-gray-50 sticky left-0 z-10 flex flex-col justify-center">
                        <div class="font-bold text-gray-800 text-sm" x-text="room.name"></div>
                        <div class="text-xs text-gray-500 truncate" x-text="room.room_type"></div>
                    </div>
                    
                    <!-- Days Grid -->
                    <div class="flex relative flex-1">
                        <template x-for="day in daysInMonth" :key="day.date">
                            <div class="w-12 flex-shrink-0 border-r border-gray-100"
                                 :class="{ 'bg-primary-50/30': isToday(day.date), 'bg-gray-50': isWeekend(day.date) }">
                            </div>
                        </template>

                        <!-- Render Bookings for this room -->
                        <template x-for="booking in getRoomBookings(room.id)" :key="'b-'+booking.id">
                            <a :href="`/admin/bookings/${booking.id}`" 
                               class="absolute h-10 top-2 rounded-md shadow-sm border border-white/20 text-xs font-medium text-white px-2 py-1 overflow-hidden cursor-pointer hover:opacity-90 transition transform hover:scale-[1.02] hover:z-20 truncate flex items-center"
                               :class="getBookingColorClass(booking.status)"
                               :style="getBookingStyle(booking)"
                               :title="`${booking.guest_name}\n${booking.check_in} s/d ${booking.check_out}\nStatus: ${booking.status_label}`">
                                <span x-text="booking.guest_name" class="truncate"></span>
                            </a>
                        </template>

                        <!-- Render Room Blocks -->
                        <template x-for="block in getRoomBlocks(room.id)" :key="'rb-'+block.id">
                            <div class="absolute h-10 top-2 rounded-md border border-gray-300 bg-gray-200 opacity-80 flex items-center justify-center text-xs text-gray-600 font-medium overflow-hidden cursor-help diagonal-stripes"
                                 :style="getBlockStyle(block)"
                                 :title="`Diblokir: ${block.reason}\n${block.start_date} s/d ${block.end_date}`">
                                <span class="bg-white/80 px-1 rounded truncate" x-text="block.reason"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            
            <template x-if="rooms.length === 0 && !loading">
                <div class="p-8 text-center text-gray-500">
                    Belum ada data kamar aktif.
                </div>
            </template>
        </div>
    </div>
</div>

<style>
.diagonal-stripes {
    background-image: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 10px,
        rgba(0,0,0,0.05) 10px,
        rgba(0,0,0,0.05) 20px
    );
}
</style>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('calendarApp', () => ({
        loading: true,
        currentDate: new Date(),
        rooms: [],
        bookings: [],
        roomBlocks: [],
        
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
                // Adjust for timezone offset to avoid JS date shifting
                const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                
                days.push({
                    date: dateString,
                    dayNumber: i,
                    dayName: date.toLocaleDateString('id-ID', { weekday: 'short' }),
                    dateObj: date
                });
            }
            return days;
        },
        
        changeMonth(delta) {
            this.currentDate.setMonth(this.currentDate.getMonth() + delta);
            this.fetchData();
        },
        
        isToday(dateString) {
            const today = new Date();
            const todayString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            return dateString === todayString;
        },
        
        isWeekend(dateString) {
            const date = new Date(dateString);
            return date.getDay() === 0 || date.getDay() === 6;
        },
        
        async fetchData() {
            this.loading = true;
            try {
                const days = this.daysInMonth;
                const startDate = days[0].date;
                const endDate = days[days.length - 1].date;
                
                const response = await fetch(`/admin/calendar/data?start_date=${startDate}&end_date=${endDate}`);
                const data = await response.json();
                
                this.rooms = data.rooms;
                this.bookings = data.bookings;
                this.roomBlocks = data.room_blocks;
            } catch (error) {
                console.error('Error fetching calendar data:', error);
                alert('Gagal mengambil data kalender.');
            } finally {
                this.loading = false;
            }
        },
        
        getRoomBookings(roomId) {
            return this.bookings.filter(b => b.room_id === roomId);
        },
        
        getRoomBlocks(roomId) {
            return this.roomBlocks.filter(b => b.room_id === roomId);
        },
        
        getBookingColorClass(status) {
            switch(status) {
                case 'pending_payment': return 'bg-pink-400';
                case 'confirmed': return 'bg-green-500';
                case 'checked_in': return 'bg-blue-500';
                case 'checked_out': return 'bg-gray-400';
                default: return 'bg-gray-500';
            }
        },
        
        // Calculate style for absolute positioning on grid
        _calculatePositionStyle(startStr, endStr) {
            const monthDays = this.daysInMonth;
            if (monthDays.length === 0) return '';
            
            const monthStart = new Date(monthDays[0].date);
            const monthEnd = new Date(monthDays[monthDays.length - 1].date);
            
            let itemStart = new Date(startStr);
            let itemEnd = new Date(endStr);
            
            // Adjust if starts before this month
            if (itemStart < monthStart) {
                itemStart = monthStart;
            }
            
            // Adjust if ends after this month
            if (itemEnd > monthEnd) {
                itemEnd = monthEnd;
            }
            
            // Cell width is 48px (w-12)
            const CELL_WIDTH = 48;
            
            // Calculate start offset (days from start of month)
            const startOffsetDays = Math.round((itemStart - monthStart) / (1000 * 60 * 60 * 24));
            
            // Calculate width (days duration)
            const durationDays = Math.round((itemEnd - itemStart) / (1000 * 60 * 60 * 24));
            
            const left = startOffsetDays * CELL_WIDTH;
            const width = Math.max(1, durationDays) * CELL_WIDTH;
            
            return `left: ${left}px; width: ${width}px;`;
        },
        
        getBookingStyle(booking) {
            return this._calculatePositionStyle(booking.check_in, booking.check_out);
        },
        
        getBlockStyle(block) {
            // Blocks are full days, so we add 1 day to end to cover it visually.
            const endParts = block.end_date.split('-');
            const endD = new Date(block.end_date);
            endD.setDate(endD.getDate() + 1);
            const endAdjusted = `${endD.getFullYear()}-${String(endD.getMonth()+1).padStart(2,'0')}-${String(endD.getDate()).padStart(2,'0')}`;
            
            return this._calculatePositionStyle(block.start_date, endAdjusted);
        }
    }));
});
</script>
@endpush
@endsection
