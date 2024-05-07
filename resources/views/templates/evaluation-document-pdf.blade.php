<html>
<head>
    <style>
        body {
        }
        h1 {
            font-size: 18px;
            text-align: center;
        }
        .table-border {
            width: 100%;
        }
        .table-border, .table-border th, .table-border td {
            border: 1px solid #000;
            border-collapse: collapse;
        }
        .pseudo-table {
            margin-left: -3px;
        }
        .left-space {
            text-align: left;
            padding: 10px;
        }
        .center {
            text-align: center;
        }
        .recommendation {
            border: 2px solid #000;
            margin: 10px auto;
            padding: 10px;
        }
        .v-align-top {
            vertical-align: top;
        }
        .asesor-ttd {
            height: 100px;
        }
    </style>
</head>
<body>
    <h1>BERITA ACARA HASIL VISITASI PERPUSTAKAAN</h1>
    <p>Berdasarkan hasil visitasi perpustakaan yang dilakukan tim asesor terhadap perpustakaan:</p>
    <table class="pseudo-table">
        <tr>
            <td style="padding-right: 5px">Nama Perpustakaan</td>
            <td>: {{ $evaluation->institution->library_name }}</td>
        </tr>
        <tr>
            <td style="padding-right: 5px">Alamat</td>
            <td>: {{ $evaluation->institution->address }}</td>
        </tr>
        <tr>
            <td style="padding-right: 5px">Waktu Penilaian</td>
            <td>: {{ $assignment->scheduled_date }}</td>
        </tr>
    </table>

    <p>Diperoleh hasil sebagai berikut:</p>
    <table class="table-border">
        <thead>
            <tr>
                <th>No</th>
                <th>Komponen</th>
                <th>Bobot</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($evaluation->evaluationResult as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="left-space">{{ $item['instrument_component'] }}</td>
                    <td class="center">{{ $item['weight'] }}</td>
                    <td class="center">{{ round($item['score'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-dark text-white">
            <tr>
                <td></td>
                <td class="left-space"><b>Jumlah</b></td>
                <td class="center"><b>{{ $evaluation->finalResult['weight'] }}</b></td>
                <td class="center"><b>{{ round($evaluation->finalResult['score'], 2) }}</b></td>
            </tr>
        </tfoot>
    </table>

    <p>Rekomendasi</p>
    @foreach ($evaluation->recommendations as $recommendation)
        <div class="recommendation">
            <div class="name">{{ $recommendation['name'] }}</div>
            <div class="content">{{ $recommendation['content'] }}</div>
        </div>
    @endforeach

    <p>
        Merujuk hasil visitasi perpustakaan, maka kami dari pihak perpustakaan yang diakreditasi menyatakan 
        PERSETUJUAN terhadap hasil visitasi perpustakaan tersebut untuk dijadikan bahan penentuan penilaian
        akreditasi oleh LAP-N terhadap perpustakaan kami.
    </p>

    <p>Pihak Penandatangan Berita Acara:</p>
    <table class="table-border">
        <thead>
            <tr>
                <th>Pihak Perpustakaan</th>
                <th>Pihak Asesor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="4" class="v-align-top left-space">
                    {{ $evaluation->institution->library_name }}<br />TTD
                </td>
            </tr>
            <tr>
                <td class="v-align-top asesor-ttd left-space">
                    @if (isset($assignment->assessors[0]))
                        Asesor ke-1<br />{{ $assignment->assessors[0]->name }}<br />TTD
                    @endif
                </td>
            </tr>
            <tr>
                <td class="v-align-top asesor-ttd left-space">
                    @if (isset($assignment->assessors[1]))
                        Asesor ke-2<br />{{ $assignment->assessors[1]->name }}<br />TTD
                    @endif
                </td>
            </tr>
            <tr>
                <td class="v-align-top asesor-ttd left-space">
                    @if (isset($assignment->assessors[2]))
                        Asesor ke-3<br />{{ $assignment->assessors[2]->name }}<br />TTD
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
