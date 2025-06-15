<?php
$url = $_GET['url'] ?? "https://agusdev.sumedangkab.go.id/api/data";
$dataJson = file_get_contents($url);
$dataObj = json_decode($dataJson, true);

$listData = $dataObj['data'];

$gender = [];
$agama = [];
$umurKategori = [
    '0-10' => 0,
    '11-20' => 0,
    '21-30' => 0,
    '31-40' => 0,
    '41-50' => 0,
    '51-60' => 0,
    '61+' => 0
];

// var_dump($listData);
// die;

foreach ($listData as $item) {
    // count gender
    $jk = $item['jenis_kelamin'];
    $gender[$jk]++;

    // count agama
    $ag = $item['agama'];
    $agama[$ag]++;

    // count umur
    $tgl_lahir = $item['tanggal_lahir'];
    $umur = date_diff(date_create($tgl_lahir), date_create('now'))->y;
    if ($umur <= 10) $umurKategori['0-10']++;
    elseif ($umur <= 20) $umurKategori['11-20']++;
    elseif ($umur <= 30) $umurKategori['21-30']++;
    elseif ($umur <= 40) $umurKategori['31-40']++;
    elseif ($umur <= 50) $umurKategori['41-50']++;
    elseif ($umur <= 60) $umurKategori['51-60']++;
    else $umurKategori['61+']++;

    // count provinsi
    $prov = $item['provinsi'];
    $provinsi[$prov]++;
}
// var_dump($agama);
// die;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Penduduk - Visualisasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-2xl md:text-3xl font-bold text-center mb-6">Visualisasi Data Penduduk</h1>

        <div class="grid md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded shadow p-4 text-center">
                <p class="text-sm text-gray-600">Total Semua Data</p>
                <p class="text-xl font-semibold"><?= $dataObj['total'] ?></p>
            </div>
            <div class="bg-white rounded shadow p-4 text-center">
                <p class="text-sm text-gray-600">Data Tampil</p>
                <p class="text-xl font-semibold"><?= count($listData) ?></p>
            </div>
            <div class="bg-white rounded shadow p-4 text-center">
                <p class="text-sm text-gray-600">Halmaan</p>
                <p class="text-xl font-semibold"><?= $dataObj['current_page'] ?>/<?= $dataObj['last_page'] ?></p>
            </div>
        </div>


        <div class="flex justify-center gap-4 mb-6">
            <?php if ($dataObj['prev_page_url']): ?>
                <a href="<?= '?url=' . $dataObj['prev_page_url'] ?>"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    &laquo; Data Sebelumnya
                </a>
            <?php else: ?>
                <span class="bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded cursor-not-allowed">
                    &laquo; Data Sebelumnya
                </span>
            <?php endif; ?>

            <?php if ($dataObj['next_page_url']): ?>
                <a href="<?= '?url=' . $dataObj['next_page_url'] ?>"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Data Selanjutnya &raquo;
                </a>
            <?php else: ?>
                <span class="bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded cursor-not-allowed">
                    Data Selanjutnya &raquo;
                </span>
            <?php endif; ?>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-semibold mb-3 text-center">Jenis Kelamin</h2>
                <canvas id="chartGender"></canvas>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-semibold mb-3 text-center">Provinsi</h2>
                <canvas id="chartProv"></canvas>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-semibold mb-3 text-center">Agama</h2>
                <canvas id="chartAgama"></canvas>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-semibold mb-3 text-center">Umur</h2>
                <canvas id="chartUmur"></canvas>
            </div>
        </div>


        <div class="flex justify-center gap-4 mb-6">
            <form method="post" action="export.php?url=<?= $url ?>">
                <button type="submit" name="export_csv" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                    Export Data to CSV
                </button>
            </form>
        </div>
        <!-- {
        "id": 11,
        "nik": "1127729522657873",
        "nama_lengkap": "Salwa Wastuti M.Ak",
        "jenis_kelamin": "Laki-laki",
        "tempat_lahir": "Banda Aceh",
        "tanggal_lahir": "2001-04-27",
        "agama": "Budha",
        "status_perkawinan": "Cerai Hidup",
        "pekerjaan": "Penambang",
        "pendidikan_terakhir": "S3",
        "kewarganegaraan": "Indonesia",
        "alamat": "Dk. Mulyadi No. 336, Subulussalam 23663, Sumbar",
        "rt": "01",
        "rw": "10",
        "dusun": "Salak",
        "desa": "Ville",
        "kecamatan": "Bau-Bau",
        "kabupaten": "Banjarbaru",
        "provinsi": "Sulawesi Barat",
        "created_at": "2025-06-12T07:10:38.000000Z",
        "updated_at": "2025-06-12T07:10:38.000000Z"
        }, -->
        <div class="overflow-x-auto mt-8 shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">RT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">RW</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Dusun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Desa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Kecamatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Kabupaten</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Provinsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tempat Lahir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tanggal Lahir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Agama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Pekerjaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Pendidikan Terakhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status Perkawinan</th>

                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($dataObj['data'] as $i => $item): ?>
                        <tr class="<?= $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $i + 1 ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['nama_lengkap'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['nik'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['alamat'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['rt'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['rw'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['dusun'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['desa'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['kecamatan'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['kabupaten'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['provinsi'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['tempat_lahir'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['tanggal_lahir'] ?? '-')  ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['agama'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['pekerjaan'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['jenis_kelamin'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['pendidikan_terakhir'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($item['status_perkawinan'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    // var_dump($gender);
    // die;
    ?>
    <script>
        // Data gender
        const dataGender = {
            labels: <?= json_encode(array_keys($gender)) ?>,
            datasets: [{
                label: 'Jumlah',
                data: <?= json_encode(array_values($gender)) ?>,
                backgroundColor: ['#4F46E5', '#EC4899', '#F59E0B']
            }]
        };
        new Chart(document.getElementById('chartGender'), {
            type: 'pie',
            data: dataGender,
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Data Provinsi
        const dataProv = {
            labels: <?= json_encode(array_keys($provinsi)) ?>,
            datasets: [{
                label: 'Jumlah',
                data: <?= json_encode(array_values($provinsi)) ?>,
                backgroundColor: ['#4F46E5', '#EC4899', '#F59E0B', '#10B981', '#EAB308', '#3B82F6', '#8B5CF6', '#F472B6', '#F87171', '#FBBF24', '#FDE047']
            }]
        };
        new Chart(document.getElementById('chartProv'), {
            type: 'doughnut',
            data: dataProv,
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });


        // Data agama
        const dataAgama = {
            labels: <?= json_encode(array_keys($agama)) ?>,
            datasets: [{
                label: 'Jumlah',
                data: <?= json_encode(array_values($agama)) ?>,
                backgroundColor: '#10B981'
            }]
        };
        new Chart(document.getElementById('chartAgama'), {
            type: 'bar',
            data: dataAgama,
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true

                    }
                }
            }
        });

        // Data umur
        const dataUmur = {
            labels: <?= json_encode(array_keys($umurKategori)) ?>,
            datasets: [{
                label: 'Jumlah',
                data: <?= json_encode(array_values($umurKategori)) ?>,
                backgroundColor: 'rgba(96, 165, 250, 0.3)',
                borderColor: '#3B82F6',
                fill: true
            }]
        };
        new Chart(document.getElementById('chartUmur'), {
            type: 'line',
            data: dataUmur,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>