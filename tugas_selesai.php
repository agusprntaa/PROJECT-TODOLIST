<?php
session_start(); 
require_once 'includes/koneksi.php';
require_once 'includes/auth.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

// Terima id dari POST (bukan GET)
if (!isset($_POST['id'])) {
    header('Location: dashboard.php');
    exit;
}

// Pastikan id_tugas adalah integer
$id_tugas = intval($_POST['id']);

// Cek status tugas saat ini
$stmt = $koneksi->prepare("SELECT status_tugas FROM tugas WHERE id_tugas = ? AND id_user = ?");
$stmt->bind_param('ii', $id_tugas, $id_user);
$stmt->execute();
$result = $stmt->get_result();
$status = $result->fetch_assoc();

if ($status && $status['status_tugas'] === 'selesai') {
    // Jika sudah selesai, ubah jadi belum
    $stmt = $koneksi->prepare("UPDATE tugas SET status_tugas = 'belum' WHERE id_tugas = ? AND id_user = ?");
    $stmt->bind_param('ii', $id_tugas, $id_user);
    $stmt->execute();
} else {
    // Jika belum selesai, ubah jadi selesai
    $stmt = $koneksi->prepare("UPDATE tugas SET status_tugas = 'selesai' WHERE id_tugas = ? AND id_user = ?");
    $stmt->bind_param('ii', $id_tugas, $id_user);
    $stmt->execute();
}

header('Location: dashboard.php');
exit;