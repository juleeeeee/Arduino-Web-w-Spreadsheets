<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    echo 'User not logged in.';
    exit();
}

// Ambil username dari sesi
$username = $_SESSION['username'];

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

// Set zona waktu ke WIB
date_default_timezone_set('Asia/Jakarta');

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/credentials.json');

$client = new \Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(\Google_Service_Sheets::SPREADSHEETS);

try {
    $service = new \Google_Service_Sheets($client);
    $spreadsheetId = '1UiCyMdIgOaB0NSirv8Wx9biclO06_iAv7XM3vtKh8CI';
    $range = 'Sheet1!A:Z'; // Adjust the range as needed
    $dumpRange = 'dump!A:Z'; // Adjust the range as needed

    // Ambil parameter jam keluar dari permintaan POST
    $jamKeluar = $_POST['jam_keluar'];

    // Membaca data dari Google Sheets
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    // Cari baris berdasarkan jam keluar
    $index = -1;
    foreach ($values as $i => $row) {
        if (isset($row[1]) && $row[1] == $jamKeluar) { // Assuming jam keluar is in the second column
            $index = $i;
            break;
        }
    }

    if ($index !== -1) {
        $rowToDelete = $values[$index];
        $currentDateTime = new DateTime();
        $deleteDate = $currentDateTime->format('Y-m-d');
        $deleteTime = $currentDateTime->format('H:i:s');

        // Menambahkan data yang dihapus ke sheet 'dump'
        $dumpValues = [
            array_merge($rowToDelete, [$deleteDate, $deleteTime, $username])
        ];

        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $dumpValues
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $service->spreadsheets_values->append($spreadsheetId, $dumpRange, $body, $params);

        // Menghapus baris dari sheet utama
        array_splice($values, $index, 1);

        // Clear the sheet first
        $clearBody = new \Google_Service_Sheets_ClearValuesRequest();
        $service->spreadsheets_values->clear($spreadsheetId, $range, $clearBody);

        // Perbarui sheet utama dengan data yang telah dimodifikasi
        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);

        $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);

        echo 'Data has been moved to dump and deleted from Sheet1.';
    } else {
        echo 'Invalid jam keluar.';
    }
} catch (Exception $e) {
    echo 'Caught exception: ', htmlspecialchars($e->getMessage());
}
?>
