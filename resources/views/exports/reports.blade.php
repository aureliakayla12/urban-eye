<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Urban Eye</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h2, p {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #e8f5e9;
        }
    </style>
</head>
<body>
    <h2>Laporan Pengaduan Masyarakat Urban Eye</h2>
    <p>Tanggal cetak: {{ $tanggalCetak->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Pelapor</th>
                <th>Kecamatan</th>
                <th>Kelurahan</th>
                <th>Alamat</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $report->title }}</td>
                    <td>{{ $report->description }}</td>
                    <td>{{ $report->category?->name ?? '-' }}</td>
                    <td>{{ $report->user?->name ?? '-' }}</td>
                    <td>{{ $report->district?->name ?? '-' }}</td>
                    <td>{{ $report->village?->name ?? '-' }}</td>
                    <td>{{ $report->address ?? '-' }}</td>
                    <td>{{ $report->created_at?->format('d-m-Y H:i') }}</td>
                    <td>{{ $report->status }}</td>
                    <td>{{ $report->verification_status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">Tidak ada laporan sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>