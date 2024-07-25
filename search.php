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
    <title>Search Data</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Include your custom stylesheet -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Fungsi untuk memuat data pencarian dari server
        function loadSearchResults(date = '') {
            var xhr = new XMLHttpRequest();
            var url = 'search_data.php';
            if (date) {
                url += '?date=' + encodeURIComponent(date);
            }
            xhr.open('GET', url, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('search-results').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Fungsi untuk pencarian data
        function searchData() {
            var date = document.getElementById('search-date').value;
            // Konversi format date (yyyy-mm-dd) ke mm/dd/yyyy
            var formattedDate = '';
            if (date) {
                var parts = date.split('-');
                formattedDate = parts[1] + '/' + parts[2] + '/' + parts[0];
            }
            loadSearchResults(formattedDate); // Memuat data dengan tanggal pencarian
        }

        // Memuat hasil pencarian awal jika ada parameter tanggal
        var urlParams = new URLSearchParams(window.location.search);
        var searchDate = urlParams.get('date');
        if (searchDate) {
            document.getElementById('search-date').value = searchDate;
            searchData();
        }
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
                <li class="nav-item active">
                    <a class="nav-link" href="search.php"><i class="fas fa-search"></i> Cari Berdasarkan Tanggal <span class="sr-only">(current)</span></a>
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
        <h1 class="mb-4">Cari Data Berdasarkan Tanggal</h1>

        <!-- Formulir Pencarian -->
        <div class="card mb-4">
            <div class="card-body">
                <label for="search-date">Cari Berdasarkan Tanggal:</label>
                <input type="date" id="search-date" class="form-control">
                <button class="btn btn-primary mt-2" onclick="searchData()">Cari</button>
            </div>
        </div>

        <div id="search-results" class="table-responsive">
            <!-- Hasil pencarian akan dimuat di sini -->
        </div>
    </div>
</body>
</html>