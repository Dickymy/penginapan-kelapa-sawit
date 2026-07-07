{{-- Global Toast Notification System --}}
{{-- Dispatches: $dispatch('toast', { type: 'success', message: '...', title: '...' }) --}}
<div x-data="{
    toasts: [],
    add(type, message, title = '') {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, type, message, title, paused: false });
        this.scheduleRemove(id);
    },
    scheduleRemove(id) {
        setTimeout(() => {
            const toast = this.toasts.find(t => t.id === id);
            if (toast && !toast.paused) {
                this.remove(id);
            } else if (toast) {
                this.scheduleRemove(id);
            }
        }, 5000);
    },
    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    },
    pause(id) {
        const toast = this.toasts.find(t => t.id === id);
        if (toast) toast.paused = true;
    },
    resume(id) {
        const toast = this.toasts.find(t => t.id === id);
        if (toast) {
            toast.paused = false;
            this.scheduleRemove(id);
        }
    }
}"
x-on:toast.window="add($event.detail.type || 'info', $event.detail.message, $event.detail.title || '')"
class="fixed top-4 right-4 z-[9999] space-y-3 pointer-events-none w-full max-w-sm px-4 sm:px-0 sm:w-96"
role="region"
aria-label="Notifikasi"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4"
             x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2 sm:translate-x-4"
             @mouseenter="pause(toast.id)"
             @mouseleave="resume(toast.id)"
             class="pointer-events-auto w-full bg-white border rounded-xl shadow-lg p-4 flex items-start gap-3"
             :class="{
                 'border-green-200': toast.type === 'success',
                 'border-red-200': toast.type === 'error',
                 'border-yellow-200': toast.type === 'warning',
                 'border-blue-200': toast.type === 'info'
             }"
             role="alert"
             aria-live="assertive"
        >
            {{-- Icon --}}
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'error'">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'warning'">
                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'info'">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </template>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <p x-show="toast.title" x-text="toast.title" class="text-sm font-semibold text-gray-900"></p>
                <p class="text-sm text-gray-600" x-text="toast.message"></p>
            </div>

            {{-- Close button --}}
            <button @click="remove(toast.id)" class="flex-shrink-0 p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

{{-- Auto-dispatch flash messages as toasts --}}
@if(session('toast_success'))
<script>document.addEventListener('alpine:init', () => { setTimeout(() => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: @json(session('toast_success')) }})), 100) })</script>
@endif
@if(session('toast_error'))
<script>document.addEventListener('alpine:init', () => { setTimeout(() => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: @json(session('toast_error')) }})), 100) })</script>
@endif
@if(session('toast_warning'))
<script>document.addEventListener('alpine:init', () => { setTimeout(() => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'warning', message: @json(session('toast_warning')) }})), 100) })</script>
@endif
@if(session('toast_info'))
<script>document.addEventListener('alpine:init', () => { setTimeout(() => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'info', message: @json(session('toast_info')) }})), 100) })</script>
@endif
