<div
    x-data="{
        show: false,
        type: 'success',
        message: '',
        init() {
            @if(session('success'))
                this.type = 'success';
                this.message = '{{ addslashes(session('success')) }}';
                this.show = true;
            @elseif(session('error'))
                this.type = 'error';
                this.message = '{{ addslashes(session('error')) }}';
                this.show = true;
            @elseif(session('warning'))
                this.type = 'warning';
                this.message = '{{ addslashes(session('warning')) }}';
                this.show = true;
            @endif
            if(this.show) setTimeout(() => this.show = false, 4000);
        }
    }"
    class="fixed top-4 right-4 z-[100]"
>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium min-w-[260px] max-w-sm"
        :class="{
            'bg-emerald-600 text-white': type === 'success',
            'bg-red-600 text-white': type === 'error',
            'bg-amber-500 text-white': type === 'warning'
        }"
        x-cloak
    >
        <span x-show="type === 'success'">✓</span>
        <span x-show="type === 'error'">✕</span>
        <span x-show="type === 'warning'">⚠</span>
        <span x-text="message" class="flex-1"></span>
        <button @click="show = false" class="opacity-70 hover:opacity-100">✕</button>
    </div>
</div>
