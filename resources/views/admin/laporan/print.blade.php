<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi - {{ $event?->nama ?? 'Event Olahraga' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 25px;
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
            padding: 7px;
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
        .text-right { text-align: right; }
        .stat-grid {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            background: #fafafa;
        }
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }
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
        <div class="title">{{ $event?->nama ?? 'SISTEM INFORMASI PEKAN OLAHRAGA' }}</div>
        <div class="subtitle">LAPORAN REKAPITULASI RESMI PENYELENGGARAAN & KLASEMEN MEDALI</div>
        <small style="color: #666;">Dicetak pada: {{ now()->translatedFormat('l, d F Y H:i') }} WIB</small>
    </div>

    <div class="section-title">1. Ringkasan Eksekutif Penyelenggaraan</div>
    <table>
        <tr>
            <td style="width: 25%; font-weight: bold;">Total Cabang Olahraga:</td>
            <td style="width: 25%;">{{ $stats['total_cabor'] }} Cabor</td>
            <td style="width: 25%; font-weight: bold;">Total Kontingen Peserta:</td>
            <td style="width: 25%;">{{ $stats['total_kontingen'] }} Daerah</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Total Atlet Terdaftar:</td>
            <td>{{ $stats['total_atlet'] }} Atlet</td>
            <td style="font-weight: bold;">Status Pertandingan:</td>
            <td>{{ $stats['pertandingan_selesai'] }} / {{ $stats['total_pertandingan'] }} Laga Selesai</td>
        </tr>
    </table>

    <div class="section-title">2. Peringkat & Perolehan Medali Kontingen</div>
    <table>
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">Rank</th>
                <th style="width: 44%;">Kontingen Daerah</th>
                <th style="width: 12%; text-align: center;">Emas</th>
                <th style="width: 12%; text-align: center;">Perak</th>
                <th style="width: 12%; text-align: center;">Perunggu</th>
                <th style="width: 12%; text-align: center;">Total Medali</th>
            </tr>
        </thead>
        <tbody>
            @forelse($klasemen as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $row['kontingen']->nama ?? $row['nama_kontingen'] ?? 'Kontingen' }}</strong></td>
                    <td class="text-center">{{ $row['emas'] }}</td>
                    <td class="text-center">{{ $row['perak'] }}</td>
                    <td class="text-center">{{ $row['perunggu'] }}</td>
                    <td class="text-center font-bold"><strong>{{ $row['total'] }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #999;">Belum ada catatan perolehan medali.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 40px; float: right; width: 220px; text-align: center;">
        <p>Ketua Panitia Pelaksana,</p>
        <div style="height: 65px;"></div>
        <p style="border-bottom: 1px solid #000; font-weight: bold;">( ......................................... )</p>
    </div>
</body>
</html>
