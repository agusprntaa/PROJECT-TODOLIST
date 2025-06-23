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

// Update status_tugas menjadi 'selesai'
$stmt = $koneksi->prepare("UPDATE tugas SET status_tugas = 'selesai' WHERE id_tugas = ? AND id_user = ?");
$stmt->bind_param('ii', $id_tugas, $id_user);
$stmt->execute();

header('Location: dashboard.php');
exit;