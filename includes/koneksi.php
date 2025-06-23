<?php
// Konfigurasi database
$host = "localhost";
$user = "root";
$password = "";
$database = "Todolist_db";

// Membuat koneksi ke database
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi 
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>