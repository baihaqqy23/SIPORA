<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rundown Acara - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 13px;
            margin-top: 4px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-left: 4px solid #2563eb;
            padding-left: 6px;
        }
        .text-center { text-align: center; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 6px 16px; cursor: pointer; background: #2563eb; color: #fff; border: none; border-radius: 4px;">
            Cetak / Download PDF
        </button>
    </div>

    <div class="header">
        <div class="title">{{ $activeEvent?->nama ?? 'SISTEM INFORMASI PEKAN OLAHRAGA' }}</div>
        <div class="subtitle">RUNDOWN ACARA & JADWAL HARIAN: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</div>
    </div>

    <div class="section-title">1. Agenda Kegiatan & Acara</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Waktu (WIB)</th>
                <th style="width: 35%;">Agenda / Acara</th>
                <th style="width: 25%;">Lokasi</th>
                <th style="width: 25%;">Penanggung Jawab</th>
            </tr>
        </thead>
        <tbody>
            @forelse($acaraList as $item)
                <tr>
                    <td class="text-center">{{ substr($item->waktu_mulai, 0, 5) }} - {{ $item->waktu_selesai ? substr($item->waktu_selesai, 0, 5) : 'Selesai' }}</td>
                    <td>
                        <strong>{{ $item->judul }}</strong>
                        @if($item->catatan)<br><small style="color: #666;">{{ $item->catatan }}</small>@endif
                    </td>
                    <td>{{ $item->lokasi ?: '-' }}</td>
                    <td>{{ $item->penanggung_jawab ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #999;">Tidak ada agenda acara seremonial pada hari ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Jadwal Pertandingan Olahraga</div>
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Waktu (WIB)</th>
                <th style="width: 20%;">Cabang Olahraga</th>
                <th style="width: 23%;">Nomor & Babak</th>
                <th style="width: 25%;">Venue / Lapangan</th>
                <th style="width: 20%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pertandinganList as $m)
                <tr>
                    <td class="text-center">{{ $m->waktu_mulai ? \Carbon\Carbon::parse($m->waktu_mulai)->format('H:i') : '-' }}</td>
                    <td><strong>{{ $m->nomorLomba?->cabangOlahraga?->nama }}</strong></td>
                    <td>{{ $m->nomorLomba?->nama }}<br><small>Babak: {{ $m->babak }}</small></td>
                    <td>{{ $m->lapangan?->venue?->nama }} ({{ $m->lapangan?->nama }})</td>
                    <td>{{ ucfirst($m->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #999;">Tidak ada jadwal pertandingan pada hari ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; float: right; width: 200px; text-align: center;">
        <p>Panitia Pelaksana,</p>
        <div style="height: 60px;"></div>
        <p style="border-bottom: 1px solid #000; font-weight: bold;">( ......................................... )</p>
    </div>
</body>
</html>
