<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Klasemen Medali - {{ $event?->nama ?? 'Pekan Olahraga' }}</title>
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
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .rank-col { width: 45px; text-align: center; font-weight: bold; }
        .medal-col { width: 65px; text-align: center; font-weight: bold; }
        .total-col { width: 75px; text-align: center; font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $event?->nama ?? 'SISTEM INFORMASI PEKAN OLAHRAGA' }}</div>
        <div class="subtitle">KLASEMEN RESMI PEROLEHAN MEDALI KONTINGEN DAERAH</div>
        <small style="color: #666;">Per Tanggal: {{ now()->translatedFormat('l, d F Y H:i') }} WIB</small>
    </div>

    <table>
        <thead>
            <tr>
                <th class="rank-col">Rank</th>
                <th>Kontingen Daerah</th>
                <th class="medal-col">Emas 🥇</th>
                <th class="medal-col">Perak 🥈</th>
                <th class="medal-col">Perunggu 🥉</th>
                <th class="total-col">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($klasemen as $row)
                <tr>
                    <td class="rank-col">{{ $row['peringkat'] }}</td>
                    <td><strong>{{ $row['kontingen_nama'] }}</strong> ({{ $row['provinsi'] ?? '-' }})</td>
                    <td class="medal-col">{{ $row['emas'] }}</td>
                    <td class="medal-col">{{ $row['perak'] }}</td>
                    <td class="medal-col">{{ $row['perunggu'] }}</td>
                    <td class="total-col">{{ $row['total'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #999;">Belum ada data perolehan medali yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
        @if($klasemen->isNotEmpty())
            <tfoot>
                <tr style="font-weight: bold; background-color: #f4f4f4;">
                    <td colspan="2" class="text-right">TOTAL KESELURUHAN MEDALI:</td>
                    <td class="medal-col">{{ $klasemen->sum('emas') }}</td>
                    <td class="medal-col">{{ $klasemen->sum('perak') }}</td>
                    <td class="medal-col">{{ $klasemen->sum('perunggu') }}</td>
                    <td class="total-col">{{ $klasemen->sum('total') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div style="margin-top: 35px; float: right; width: 220px; text-align: center;">
        <p>Panitia Pelaksana Bidang Pertandingan,</p>
        <div style="height: 60px;"></div>
        <p style="border-bottom: 1px solid #000; font-weight: bold;">( ......................................... )</p>
    </div>
</body>
</html>
