<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit();
}

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/credentials.json');

$client = new \Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(\Google_Service_Sheets::SPREADSHEETS);

try {
    $service = new \Google_Service_Sheets($client);
    $spreadsheetId = '1UiCyMdIgOaB0NSirv8Wx9biclO06_iAv7XM3vtKh8CI';
    $range = 'Sheet1!A:Z'; // Adjust the range as needed

    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (empty($values)) {
        echo "<div class='alert alert-warning'>No data found.</div>";
    } else {
        // Sorting the data by 'Tanggal Keluar' and 'Jam Keluar' in descending order
        usort($values, function ($a, $b) {
            $datetimeA = DateTime::createFromFormat('m/d/Y H:i:s', $a[2] . ' ' . $a[1]); // Adjust the date and time format accordingly
            $datetimeB = DateTime::createFromFormat('m/d/Y H:i:s', $b[2] . ' ' . $b[1]);
            return $datetimeB <=> $datetimeA;
        });

        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>ID Barang</th><th>Jam Keluar</th><th>Tanggal Keluar</th><th>Tempat</th><th>Merek</th><th>Jenis</th>
            <th>Harga Beli</th><th>Harga Jual</th><th>Aksi</th></tr></thead>";
        echo "<tbody>";
        foreach ($values as $index => $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "<td><button class='btn btn-danger btn-sm' onclick='deleteRow(\"" . htmlspecialchars($row[1]) . "\")'>Hapus</button></td>"; // Assuming jam keluar is in the second column
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Caught exception: ',  htmlspecialchars($e->getMessage()), '</div>';
}
?>
