<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Penghapusan Data</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <script>
        // Fungsi untuk memuat data riwayat dari server
        function loadHistory() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_history.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('history-table').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Fungsi untuk merestore data
        function restoreRow(waktuPenghapusan) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'restore.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    loadHistory(); // Reload history after restoration
                }
            };
            xhr.send('waktu_penghapusan=' + encodeURIComponent(waktuPenghapusan));
        }

        // Memuat data riwayat awal
        loadHistory();
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Data Manager</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search.php"><i class="fas fa-search"></i> Cari Berdasarkan Tanggal</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="history.php"><i class="fas fa-history"></i> History Penghapusan <span class="sr-only">(current)</span></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white" href="index.php?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">History Penghapusan Data</h1>
        <div id="history-table" class="table-responsive">
            <!-- Tabel riwayat penghapusan akan dimuat di sini -->
        </div>
    </div>
</body>
</html>
