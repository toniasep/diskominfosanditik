<?php
if (isset($_POST['export_csv'])) {
    $url = $_GET['url'] ?? "https://agusdev.sumedangkab.go.id/api/data";
    $dataJson = file_get_contents($url);
    $dataObj = json_decode($dataJson, true);
    $data = $dataObj['data'] ?? [];

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=data_sumedang.csv');

    $output = fopen('php://output', 'w');

    if (!empty($data)) {
    fputcsv($output, array_keys($data[0]));
    }

    foreach ($data as $row) {
    fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

?>