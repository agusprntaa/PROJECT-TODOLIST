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

    // Query dengan prepared statement untuk mencegah SQL Injection
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah email ditemukan
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

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

    // Cek apakah email sudah terdaftar
    $stmt = $koneksi->prepare("SELECT id_user FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return false; // Email sudah terdaftar
    }

    // Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Query untuk menambahkan pengguna baru dengan prepared statement
    $stmt = $koneksi->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $email, $hashed_password);
    $result = $stmt->execute();

    return $result; // True jika berhasil, false jika gagal
}
?>