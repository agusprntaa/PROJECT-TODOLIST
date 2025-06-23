<?php
session_start();
require_once 'includes/koneksi.php';
require_once 'includes/auth.php';
require_once 'includes/flash.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

// Cek apakah id tugas diberikan
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id_tugas = intval($_GET['id']);

// Hapus tugas berdasarkan id dan user
$stmt = $koneksi->prepare("DELETE FROM tugas WHERE id_tugas = ? AND id_user = ?");
$stmt->bind_param('ii', $id_tugas, $id_user);

if ($stmt->execute()) {
    set_flash('Tugas berhasil dihapus!', 'success');
} else {
    set_flash('Gagal menghapus tugas.', 'error');
}

header('Location: dashboard.php');
exit;