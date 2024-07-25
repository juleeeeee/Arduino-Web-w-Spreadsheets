<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit();
}

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING); // Menyembunyikan peringatan E_DEPRECATED dan E_WARNING

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/credentials.json');

$client = new \Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(\Google_Service_Sheets::SPREADSHEETS);

try {
    $service = new \Google_Service_Sheets($client);
    $spreadsheetId = '1UiCyMdIgOaB0NSirv8Wx9biclO06_iAv7XM3vtKh8CI';
    $range = 'Sheet1';

    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (empty($values)) {
        echo "<div class='alert alert-warning'>No data found.</div>";
    } else {
        $searchDate = isset($_GET['date']) ? $_GET['date'] : '';

        // Filter dan urutkan data berdasarkan tanggal (kolom C)
        $filteredValues = [];
        foreach ($values as $row) {
            if (!empty($searchDate)) {
                if (isset($row[2])) {
                    $rowDate = DateTime::createFromFormat('m/d/Y', $row[2]);
                    $searchDateFormatted = DateTime::createFromFormat('m/d/Y', $searchDate);
                    if ($rowDate && $searchDateFormatted && $rowDate->format('Y-m-d') === $searchDateFormatted->format('Y-m-d')) {
                        $filteredValues[] = $row;
                    }
                }
            } else {
                $filteredValues[] = $row;
            }
        }

        usort($filteredValues, function ($a, $b) {
            $dateA = DateTime::createFromFormat('m/d/Y H:i', $a[2] . ' ' . $a[1]);
            $dateB = DateTime::createFromFormat('m/d/Y H:i', $b[2] . ' ' . $b[1]);
            return $dateB <=> $dateA;
        });

        if (empty($filteredValues)) {
            echo "<div class='alert alert-warning'>No data found for the selected date.</div>";
        } else {
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead><tr><th>ID Barang</th><th>Jam Keluar</th><th>Tanggal Keluar</th><th>Tempat</th><th>Merek</th><th>Jenis</th>
                <th>Harga Beli</th><th>Harga Jual</th><th>Aksi</th></tr></thead>";
            echo "<tbody>";
            foreach ($filteredValues as $index => $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "<td><button class='btn btn-danger btn-sm' onclick='deleteRow($index)'>Hapus</button></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Caught exception: ',  htmlspecialchars($e->getMessage()), '</div>';
}
?>
