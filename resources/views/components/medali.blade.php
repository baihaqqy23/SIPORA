@props(['emas' => 0, 'perak' => 0, 'perunggu' => 0])

<div class="flex items-center gap-2">
    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums" style="background:#FEF3C7; color:#92400E;">
        <span>🥇</span> {{ $emas }}
    </span>
    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums" style="background:#F1F5F9; color:#475569;">
        <span>🥈</span> {{ $perak }}
    </span>
    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums" style="background:#FEF3C7; color:#78350F;">
        <span>🥉</span> {{ $perunggu }}
    </span>
</div>
