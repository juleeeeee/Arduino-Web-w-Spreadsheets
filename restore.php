<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    echo 'User not logged in.';
    exit();
}

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/credentials.json');

$client = new \Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(\Google_Service_Sheets::SPREADSHEETS);

try {
    $service = new \Google_Service_Sheets($client);
    $spreadsheetId = '1UiCyMdIgOaB0NSirv8Wx9biclO06_iAv7XM3vtKh8CI';
    $range = 'Sheet1!A:Z'; // Adjust the range as needed
    $dumpRange = 'dump!A:Z'; // Adjust the range as needed

    // Ambil parameter waktu penghapusan dari permintaan POST
    $waktuPenghapusan = $_POST['waktu_penghapusan'];

    // Membaca data dari Google Sheets
    $response = $service->spreadsheets_values->get($spreadsheetId, $dumpRange);
    $dumpValues = $response->getValues();

    // Cari baris berdasarkan waktu penghapusan
    $index = -1;
    foreach ($dumpValues as $i => $row) {
        if (isset($row[9]) && $row[9] == $waktuPenghapusan) { // Assuming waktu penghapusan is in the 10th column
            $index = $i;
            break;
        }
    }

    if ($index !== -1) {
        $recordToRestore = $dumpValues[$index];

        // Remove the columns for 'Tanggal Penghapusan', 'Waktu Penghapusan', and 'Username'
        $recordToRestore = array_slice($recordToRestore, 0, 8);

        // Insert the record back into the main index (Sheet1)
        $body = new \Google_Service_Sheets_ValueRange([
            'values' => [$recordToRestore]
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

        // Remove the restored record from 'dump'
        array_splice($dumpValues, $index, 1);

        // Clear the dump sheet first
        $clearBody = new \Google_Service_Sheets_ClearValuesRequest();
        $service->spreadsheets_values->clear($spreadsheetId, $dumpRange, $clearBody);

        // Update the dump sheet with the remaining data
        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $dumpValues
        ]);

        if (!empty($dumpValues)) {
            $service->spreadsheets_values->update($spreadsheetId, $dumpRange, $body, $params);
        }

        echo 'Record has been restored and removed from history.';
    } else {
        echo 'Invalid waktu penghapusan.';
    }
} catch (Exception $e) {
    echo 'Caught exception: ', htmlspecialchars($e->getMessage());
}
?>
