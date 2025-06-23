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
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}
$id_tugas = intval($_GET['id']);

// Ambil data tugas
$stmt = $koneksi->prepare("SELECT * FROM tugas WHERE id_tugas=? AND id_user=?");
$stmt->bind_param('ii', $id_tugas, $id_user);
$stmt->execute();
$result = $stmt->get_result();
$tugas = $result->fetch_assoc();

if (!$tugas) {
    echo "<div class='text-center text-red-500 mt-10'>Tugas tidak ditemukan.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    <main class="flex-1 px-2 md:px-10 py-8 bg-gray-50">
        <?php show_flash(); // menampilkan flash message ?>
        <div class="max-w-xl mx-auto bg-white rounded-2xl shadow p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="material-icons text-base text-[#328E6E]">info</span>
                Detail Tugas
            </h2>
            <div class="space-y-3">
                <div>
                    <span class="text-gray-500 text-xs">Judul:</span>
                    <div class="font-semibold text-lg"><?php echo htmlspecialchars($tugas['nama_tugas']); ?></div>
                </div>
                <div>
                    <span class="text-gray-500 text-xs">Deadline:</span>
                    <div><?php echo htmlspecialchars($tugas['tanggal_deadline']); ?></div>
                </div>
                <div>
                    <span class="text-gray-500 text-xs">Status:</span>
                    <div>
                        <?php if ($tugas['status_tugas'] === 'selesai'): ?>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <span class="material-icons text-sm mr-1">check_circle</span> Selesai
                            </span>
                        <?php else: ?>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                <span class="material-icons text-sm mr-1">pending_actions</span> Belum
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <span class="text-gray-500 text-xs">Prioritas:</span>
                    <div><?php echo htmlspecialchars($tugas['prioritas']); ?></div>
                </div>
                <div>
                    <span class="text-gray-500 text-xs">Deskripsi:</span>
                    <div class="whitespace-pre-line"><?php echo htmlspecialchars($tugas['deskripsi'] ?? '-'); ?></div>
                </div>
            </div>
            <div class="flex gap-2 mt-8">
                <a href="edit_tugas.php?id=<?php echo $tugas['id_tugas']; ?>"
                    class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition flex items-center gap-1">
                    <span class="material-icons text-base">edit</span> Edit
                </a>
                <a href="hapus_tugas.php?id=<?php echo $tugas['id_tugas']; ?>"
                    onclick="return confirm('Yakin ingin menghapus tugas ini?')"
                    class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition flex items-center gap-1">
                    <span class="material-icons text-base">delete</span> Hapus
                </a>
                <a href="dashboard.php"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-1">
                    <span class="material-icons text-base">arrow_back</span> Kembali
                </a>
            </div>
        </div>
    </main>
</body>

</html>