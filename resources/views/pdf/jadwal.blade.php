<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pertandingan - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 15px;
            font-size: 11px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 12px;
            margin-top: 4px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .match-badge {
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            padding: 4px;
            border-radius: 4px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">JADWAL PERTANDINGAN HARIAN</div>
        <div class="subtitle">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;" class="text-center">Jam Mulai</th>
                @foreach($lapangan as $lap)
                    <th>
                        {{ $lap->nama }}<br>
                        <small style="font-weight: normal; color: #666;">{{ $lap->venue?->nama }}</small>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($matrix['slots'] as $slot)
                <tr>
                    <td class="text-center font-bold" style="background-color: #fafafa;">{{ $slot }}</td>
                    @foreach($lapangan as $lap)
                        @php
                            $m = $matrix['matrix'][$lap->id][$slot] ?? null;
                        @endphp
                        <td style="vertical-align: top; height: 35px;">
                            @if($m)
                                <div class="match-badge">
                                    <strong>{{ $m->nomorLomba?->cabangOlahraga?->nama }} ({{ $m->babak }})</strong><br>
                                    {{ $m->nomorLomba?->nama }}<br>
                                    <small style="color: #444;">
                                        @if($m->pesertaPertandingan->count() >= 2)
                                            {{ $m->pesertaPertandingan[0]->peserta?->nama ?? 'TBD' }} vs {{ $m->pesertaPertandingan[1]->peserta?->nama ?? 'TBD' }}
                                        @else
                                            TBD
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; float: right; width: 200px; text-align: center;">
        <p>Panitia Bidang Pertandingan,</p>
        <div style="height: 50px;"></div>
        <p style="border-bottom: 1px solid #000; font-weight: bold;">( ..................................... )</p>
    </div>
</body>
</html>
