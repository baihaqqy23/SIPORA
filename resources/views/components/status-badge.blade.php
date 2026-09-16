@props(['status'])

@php
$classes = match($status) {
    'draft', 'menunggu', 'belum_mulai', 'belum' => 'bg-slate-100 text-slate-700',
    'disetujui', 'terverifikasi', 'selesai', 'aktif', 'terkunci' => 'bg-emerald-100 text-emerald-700',
    'ditolak', 'ditolak_sistem', 'didiskualifikasi', 'dibatalkan', 'nonaktif' => 'bg-red-100 text-red-700',
    'revisi', 'perlu_perbaikan', 'ditunda' => 'bg-amber-100 text-amber-700',
    'berlangsung', 'dipublikasikan', 'terjadwal', 'diverifikasi_cabor' => 'bg-blue-100 text-blue-700',
    default => 'bg-slate-100 text-slate-600',
};

$label = match($status) {
    'draft' => 'Draft',
    'menunggu' => 'Menunggu',
    'belum_mulai' => 'Belum Mulai',
    'belum' => 'Belum',
    'disetujui' => 'Disetujui',
    'terverifikasi' => 'Terverifikasi',
    'diverifikasi_cabor' => 'Verif. Cabor',
    'selesai' => 'Selesai',
    'aktif' => 'Aktif',
    'terkunci' => 'Terkunci',
    'ditolak' => 'Ditolak',
    'ditolak_sistem' => 'Ditolak Sistem',
    'didiskualifikasi' => 'Didiskualifikasi',
    'dibatalkan' => 'Dibatalkan',
    'nonaktif' => 'Nonaktif',
    'revisi' => 'Perlu Revisi',
    'perlu_perbaikan' => 'Perlu Perbaikan',
    'ditunda' => 'Ditunda',
    'berlangsung' => 'Berlangsung',
    'dipublikasikan' => 'Dipublikasikan',
    'terjadwal' => 'Terjadwal',
    'tergenerate' => 'Ter-generate',
    default => ucfirst(str_replace('_', ' ', $status)),
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium $classes"]) }}>
    @if(in_array($status, ['berlangsung', 'dipublikasikan']))
        <span class="h-1.5 w-1.5 rounded-full bg-current animate-pulse"></span>
    @endif
    {{ $label }}
</span>
