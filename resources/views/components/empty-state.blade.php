@props(['icon' => null, 'title' => 'Belum ada data', 'description' => '', 'action' => null, 'actionHref' => null, 'actionText' => ''])

<div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
        @if($icon)
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">{{ $icon }}</svg>
        @else
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        @endif
    </div>
    <h3 class="text-sm font-semibold text-slate-700">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-slate-400 max-w-xs">{{ $description }}</p>
    @endif
    {{ $slot }}
    @if($actionHref && $actionText)
        <a href="{{ $actionHref }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand/90">
            {{ $actionText }}
        </a>
    @elseif($action)
        <div class="mt-4">{{ $action }}</div>
    @endif
</div>
