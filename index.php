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
            <!-- Tombol Prev -->
            <?php if ($dataObj['prev_page_url']): ?>
                <a href="<?= '?url=' . $dataObj['prev_page_url'] ?>"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    &laquo; Prev
                </a>
            <?php else: ?>
                <span class="bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded cursor-not-allowed">
                    &laquo; Prev
                </span>
            <?php endif; ?>

            <?php if ($dataObj['next_page_url']): ?>
                <a href="<?= '?url=' . $dataObj['next_page_url'] ?>"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Data Sebelumnya &raquo;
                </a>
            <?php else: ?>
                <span class="bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded cursor-not-allowed">
                    Data Selanjutnya &raquo;
                </span>
            <?php endif; ?>

            <form method="post" action="export.php?url=<?= $url ?>">
                <button type="submit" name="export_csv" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                    Export to CSV
                </button>
            </form>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-semibold mb-3 text-center">Jenis Kelamin</h2>
                <canvas id="chartGender"></canvas>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-semibold mb-3 text-center">Agama</h2>
                <canvas id="chartAgama"></canvas>
            </div>
        </div>

        <div class="bg-white mt-6 p-4 rounded shadow">
            <h2 class="text-lg font-semibold mb-3 text-center">Umur</h2>
            <canvas id="chartUmur"></canvas>
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
                        position: 'top'
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
                // maintainAspectRatio: false,
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