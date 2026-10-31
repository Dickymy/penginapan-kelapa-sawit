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
                <div class="w-32 md:w-48 flex-shrink-0 border-r border-gray-200 p-3 font-semibold text-gray-700 bg-gray-50 sticky left-0 z-30 flex items-center">
                    Kamar
                </div>
                <template x-for="day in daysInMonth" :key="day.date">
                    <div class="w-16 flex-shrink-0 border-r border-gray-200 p-2 text-center text-xs font-medium text-gray-500"
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
                    <div class="w-32 md:w-48 flex-shrink-0 border-r border-gray-200 p-2 bg-white group-hover:bg-gray-50 sticky left-0 z-10 flex flex-col justify-center">
                        <div class="font-bold text-gray-800 text-sm truncate" x-text="room.name" :title="room.name"></div>
                        <div class="text-xs text-gray-500 truncate" x-text="room.room_type" :title="room.room_type"></div>
                    </div>
                    
                    <!-- Days Grid -->
                    <div class="flex relative flex-1">
                        <template x-for="day in daysInMonth" :key="day.date">
                            <div class="w-16 flex-shrink-0 border-r border-gray-100"
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
                                 :title="`Diblokir: ${block.reason}\n${block.start_date} s/d ${block.end_date ? block.end_date : 'Seterusnya'}`">
                                <span class="bg-white/80 px-1 rounded truncate text-gray-700" x-text="block.reason"></span>
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
                    const dateString = formatDateStr(date);
                    
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
                const newDate = new Date(this.currentDate);
                newDate.setDate(1); // Set to 1 to prevent month overflow
                newDate.setMonth(newDate.getMonth() + delta);
                this.currentDate = newDate;
                this.fetchData();
            },
            
            isToday(dateString) {
                const today = new Date();
                return dateString === formatDateStr(today);
            },
            
            isWeekend(dateString) {
                const date = parseLocalDate(dateString);
                return date ? (date.getDay() === 0 || date.getDay() === 6) : false;
            },
            
            async fetchData() {
                this.loading = true;
                try {
                    const days = this.daysInMonth;
                    if (days.length === 0) return;
                    const startDate = days[0].date;
                    const endDate = days[days.length - 1].date;
                    
                    const response = await fetch(`/admin/calendar/data?start_date=${startDate}&end_date=${endDate}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    
                    this.rooms = data.rooms || [];
                    this.bookings = data.bookings || [];
                    this.roomBlocks = data.room_blocks || [];
                } catch (error) {
                    console.error('Error fetching calendar data:', error);
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
                    case 'pending_payment': return 'bg-pink-500';
                    case 'confirmed': return 'bg-green-600';
                    case 'checked_in': return 'bg-blue-600';
                    case 'checked_out': return 'bg-gray-400';
                    default: return 'bg-gray-500';
                }
            },
            
            // Calculate style for absolute positioning on grid
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
                
                // If item is completely before or after this month view
                if (itemEnd <= gridStart || itemStart >= gridEnd) {
                    return 'display: none;';
                }
                
                // Clamp item range to the visible month window
                if (itemStart < gridStart) {
                    itemStart = gridStart;
                }
                if (itemEnd > gridEnd) {
                    itemEnd = gridEnd;
                }
                
                // Cell width is 64px (w-16)
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
                    // Indefinite block extends through the end of the displayed month
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
