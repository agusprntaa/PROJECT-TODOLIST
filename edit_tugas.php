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

// Ambil data tugas berdasarkan id
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id_tugas = intval($_GET['id']);

// Proses update tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);
    $tanggal = $_POST['tanggal']; // format: YYYY-MM-DD
    $jam = $_POST['jam'];         // format: HH:MM
    $tanggal_deadline = $tanggal . ' ' . $jam . ':00'; // format: YYYY-MM-DD HH:MM:SS
    $prioritas = $_POST['prioritas'];
    $status_tugas = $_POST['status_tugas'];
    $deskripsi = $_POST['deskripsi'];

    // Validasi input
    $stmt = $koneksi->prepare("UPDATE tugas SET nama_tugas=?, tanggal_deadline=?, prioritas=?, status_tugas=?, deskripsi=? WHERE id_tugas=? AND id_user=?");
    $stmt->bind_param('sssssii', $judul, $tanggal_deadline, $prioritas, $status_tugas, $deskripsi, $id_tugas, $id_user);
    $stmt->execute();

    // Cek apakah update berhasil
    set_flash('Tugas berhasil diupdate!', 'success');
    header('Location: dashboard.php');
    exit;
}

// Ambil data tugas untuk ditampilkan di form
$stmt = $koneksi->prepare("SELECT * FROM tugas WHERE id_tugas=? AND id_user=?");
$stmt->bind_param('ii', $id_tugas, $id_user);
$stmt->execute();
$result = $stmt->get_result();
$tugas = $result->fetch_assoc();

// Cek apakah tugas ditemukan
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
    <title>Edit Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    <main class="flex-1 px-2 md:px-10 py-8 bg-gray-50">
        <div class="max-w-xl mx-auto bg-white rounded-2xl shadow p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="material-icons text-base text-[#328E6E]">edit</span>
                Edit Tugas
            </h2>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-gray-700 mb-1">Judul Tugas</label>
                    <input type="text" name="judul" required
                        value="<?php echo htmlspecialchars($tugas['nama_tugas']); ?>"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1">Tanggal Deadline</label>
                        <input type="date" name="tanggal" required
                            value="<?php echo isset($tugas) ? substr($tugas['tanggal_deadline'], 0, 10) : ''; ?>"
                            class="w-full px-4 py-2 border rounded-lg" />
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1">Jam Deadline</label>
                        <input type="time" name="jam" required
                            value="<?php echo isset($tugas) ? substr($tugas['tanggal_deadline'], 11, 5) : ''; ?>"
                            class="w-full px-4 py-2 border rounded-lg" />
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Prioritas</label>
                    <select name="prioritas"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="high" <?php if ($tugas['prioritas'] == 'high')
                            echo 'selected'; ?>>High</option>
                        <option value="medium" <?php if ($tugas['prioritas'] == 'medium')
                            echo 'selected'; ?>>Medium
                        </option>
                        <option value="low" <?php if ($tugas['prioritas'] == 'low')
                            echo 'selected'; ?>>Low</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Status</label>
                    <select name="status_tugas"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        <option value="belum" <?php if ($tugas['status_tugas'] == 'belum')
                            echo 'selected'; ?>>Belum
                        </option>
                        <option value="selesai" <?php if ($tugas['status_tugas'] == 'selesai')
                            echo 'selected'; ?>>Selesai
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200"><?php echo htmlspecialchars($tugas['deskripsi']); ?></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-[#328E6E] text-white px-6 py-2 rounded-lg hover:bg-[#256d52] transition">Simpan
                        Perubahan</button>
                    <a href="dashboard.php"
                        class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">Batal</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>