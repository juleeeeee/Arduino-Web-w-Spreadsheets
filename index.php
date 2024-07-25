<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit();
}

// Fungsi untuk menangani logout
if (isset($_GET['logout']) && $_GET['logout'] == true) {
    logout();
}

function logout() {
    // Menghancurkan sesi
    session_destroy();

    // Redirect ke halaman login
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data apa?</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <script>
        // Function to load data from server
        function loadData() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_data.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('data-table').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Function to delete a row
        function deleteRow(jamKeluar) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_row.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    loadData(); // Reload data after deletion
                }
            };
            xhr.send('jam_keluar=' + encodeURIComponent(jamKeluar));
        }

        // Load data initially
        loadData();

        // Set interval to auto-refresh every 5 seconds (5000 ms)
        setInterval(loadData, 5000);
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
                <li class="nav-item active">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search.php"><i class="fas fa-search"></i> Cari Berdasarkan Tanggal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="history.php"><i class="fas fa-history"></i> History Penghapusan</a>
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
        <h1 class="mb-4 text-center">Data apa?</h1>
        <div id="data-table" class="table-responsive shadow-lg">
            <!-- Tabel akan dimuat di sini -->
        </div>
    </div>
</body>
</html>
