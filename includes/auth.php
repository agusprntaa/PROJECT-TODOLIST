<?php
// Mulai sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database
require 'koneksi.php';

// Fungsi untuk login pengguna
function login($email, $password) {
    global $koneksi;

    // Cegah SQL Injection
    $email = mysqli_real_escape_string($koneksi, $email);
    $password = mysqli_real_escape_string($koneksi, $password);

    // Query untuk memeriksa email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);

    // Periksa apakah email ditemukan
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan data pengguna ke sesi
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama'];

            return true; // Login berhasil
        } else {
            return false; // Password salah
        }
    } else {
        return false; // Email tidak ditemukan
    }
}

// Fungsi untuk registrasi pengguna
function register($nama, $email, $password) {
    global $koneksi;

    // Cegah SQL Injection
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $email = mysqli_real_escape_string($koneksi, $email);
    $password = mysqli_real_escape_string($koneksi, $password);

    // Cek apakah email sudah terdaftar
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        return false; // Email sudah terdaftar
    }

    // Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Query untuk menambahkan pengguna baru
    $query = "INSERT INTO users (nama, email, password) VALUES ('$nama', '$email', '$hashed_password')";
    $result = mysqli_query($koneksi, $query);

    return $result; // True jika berhasil, false jika gagal
}

?>