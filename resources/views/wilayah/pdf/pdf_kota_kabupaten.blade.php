<!DOCTYPE html>
<html>

<head>
    <title>Data Kota/Kabupaten</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>

    <p><strong>Tanggal Cetak:</strong> {{ $tanggal }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kota/Kabupaten</th>
                <th>Provinsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item['nama_kota_kabupaten'] }}</td>
                <td>{{ $item['provinsi']['nama_provinsi'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>



</html>