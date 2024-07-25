<?php
require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/credentials.json');

$client = new \Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(\Google_Service_Sheets::SPREADSHEETS);

try {
    $service = new \Google_Service_Sheets($client);
    $spreadsheetId = '1UiCyMdIgOaB0NSirv8Wx9biclO06_iAv7XM3vtKh8CI';
    $dumpRange = 'dump!A:Z'; // Adjust the range as needed

    // Membaca data dari sheet 'dump'
    $response = $service->spreadsheets_values->get($spreadsheetId, $dumpRange);
    $values = $response->getValues();

    if (empty($values)) {
        echo "<div class='alert alert-warning'>No data found.</div>";
    } else {
        // Mengurutkan data berdasarkan tanggal dan waktu penghapusan dari yang terbaru
        usort($values, function($a, $b) {
            $dateA = $a[8] . ' ' . $a[9]; // Format: 'Y-m-d H:i:s'
            $dateB = $b[8] . ' ' . $b[9];
            return strtotime($dateB) - strtotime($dateA);
        });

        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>ID Barang</th><th>Jam Keluar</th><th>Tanggal Keluar</th><th>Tempat</th><th>Merek</th><th>Jenis</th>
            <th>Harga Beli</th><th>Harga Jual</th><th>Tanggal Penghapusan</th><th>Waktu Penghapusan</th><th>Username</th><th>Aksi</th></tr></thead>";
        echo "<tbody>";
        foreach ($values as $index => $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "<td><button class='btn btn-success btn-sm' onclick='restoreRow(\"" . htmlspecialchars($row[9]) . "\")'>Restore</button></td>"; // Assuming waktu penghapusan is in the 10th column
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Caught exception: ', htmlspecialchars($e->getMessage()), '</div>';
}
?>
