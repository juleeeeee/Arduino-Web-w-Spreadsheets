<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/credentials.json');

$client = new \Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(\Google_Service_Sheets::SPREADSHEETS);

try {
    $service = new \Google_Service_Sheets($client);
    $spreadsheetId = '1UiCyMdIgOaB0NSirv8Wx9biclO06_iAv7XM3vtKh8CI';
    $loginRange = 'Sheet2';

    // Cek apakah formulir login sudah disubmit
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Membaca data dari Google Sheets
        $response = $service->spreadsheets_values->get($spreadsheetId, $loginRange);
        $values = $response->getValues();

        // Periksa kredensial login
        $loginSuccessful = false;
        foreach ($values as $row) {
            if (isset($row[0]) && isset($row[1]) && $row[0] === $username && $row[1] === $password) {
                $loginSuccessful = true;
                break;
            }
        }

        if ($loginSuccessful) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
} catch (Exception $e) {
    $error = 'Caught exception: ' . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Login</h2>
        </div>
        <?php if (isset($error)) { echo "<div class='error-message'>$error</div>"; } ?>
        <form class="login-form" method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>