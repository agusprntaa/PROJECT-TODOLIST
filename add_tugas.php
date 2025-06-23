<?php
session_start();
require_once 'includes/koneksi.php'; // Koneksi ke database
require_once 'includes/auth.php'; // Autentikasi pengguna
require_once 'includes/flash.php';  // Untuk menampilkan pesan flash

if (!isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit;
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];

// Proses tambah tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['judul'])) {
    $judul = trim($_POST['judul']);
    $tanggal = $_POST['tanggal']; // format: YYYY-MM-DD
    $jam = $_POST['jam'];         // format: HH:MM
    $tanggal_deadline = $tanggal . ' ' . $jam . ':00'; // format: YYYY-MM-DD HH:MM:SS
    $status = 'belum'; // ENUM: 'belum' atau 'selesai'
    $prioritas = isset($_POST['prioritas']) ? $_POST['prioritas'] : 'medium';
    $list = isset($_POST['list']) && $_POST['list'] !== '' ? $_POST['list'] : 'General';
    $deskripsi = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';
    $stmt = $koneksi->prepare("INSERT INTO tugas (id_user, nama_tugas, tanggal_deadline, status_tugas, prioritas, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssss', $id_user, $judul, $tanggal_deadline, $status, $prioritas, $deskripsi);
    $stmt->execute();

    // Setelah berhasil tambah tugas
    set_flash('Tugas berhasil ditambahkan!', 'success');
    header('Location: dashboard.php');
    exit;
}

// Ambil semua tugas user
$query = "SELECT * FROM tugas WHERE id_user = ? ORDER BY tanggal_deadline ASC";
$stmt = $koneksi->prepare($query);
$stmt->bind_param('i', $id_user);
$stmt->execute();
$result = $stmt->get_result();
$tugas = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    <main class="flex-1 px-2 md:px-10 py-8 bg-gray-50">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-1">To-Do List</h2>
            <p class="text-gray-500 text-base">Kelola dan catat semua tugasmu di sini.</p>
        </div>

        <!-- Form Tambah Tugas -->
        <div class="bg-white rounded-2xl shadow p-6 mb-8 w-full max-w-7xl">
            <form method="POST" class="flex flex-col gap-4">
                <input type="text" name="judul" required placeholder="Judul tugas..."
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                <div class="flex gap-2 flex-col md:flex-row w-full">
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1">Tanggal Deadline</label>
                        <input type="date" name="tanggal" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1">Jam Deadline</label>
                        <input type="time" name="jam" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                    </div>
                </div>
                <select name="prioritas"
                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="high">High</option>
                    <option value="medium" selected>Medium</option>
                    <option value="low">Low</option>
                </select>
                <!-- Deskripsi -->
                <div>
                    <label class="block text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi"
                        class="w-full px-4 py-2 text-sm rounded-lg border border-[#ccc] focus:outline-none focus:ring-2 focus:ring-[#00704a] resize-none"
                        placeholder="Tulis deskripsi tugas (opsional)..." rows="4"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                </div>
                <button type="submit"
                    class="flex items-center gap-1 bg-[#328E6E] text-white px-4 py-2 rounded-lg hover:bg-[#256d52] transition">
                    <span class="material-icons text-base">add</span> Tambah
                </button>
            </form>
        </div>

        <!-- Daftar Tugas -->
        <div class="bg-white rounded-2xl shadow p-8 w-full max-w-full">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Daftar Tugas</h3>
            </div>
            <div class="overflow-y-auto" style="max-height: 420px;">
                <?php if (count($tugas) === 0): ?>
                    <div class="text-center text-gray-400 py-10">Belum ada tugas.</div>
                <?php else: ?>
                    <ul class="space-y-4 w-full">
                        <?php foreach ($tugas as $tugas): ?>
                            <li
                                class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 rounded-xl px-5 py-4 shadow-sm transition w-full">
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-[#328E6E] rounded" <?php if ($tugas['status_tugas'] === 'selesai')
                                        echo 'checked disabled'; ?> />
                                    <div>
                                        <div class="text-xs text-gray-400 mb-1 font-semibold flex items-center gap-1">
                                            <span class="material-icons text-sm align-middle">folder</span>
                                            <?php echo htmlspecialchars($tugas['list'] ?? 'General'); ?>
                                        </div>
                                        <div class="font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($tugas['nama_tugas']); ?>
                                        </div>
                                        <div class="text-xs text-gray-400">Deadline:
                                            <?php
                                            $deadline = date('d-m-Y H:i', strtotime($tugas['tanggal_deadline']));
                                            echo htmlspecialchars($deadline);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <?php
                                    $prioritas = isset($tugas['prioritas']) ? strtolower($tugas['prioritas']) : 'medium';
                                    $badge = [
                                        'high' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'High'],
                                        'medium' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Medium'],
                                        'low' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Low'],
                                    ];
                                    $b = $badge[$prioritas] ?? $badge['medium'];
                                    ?>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold <?php echo $b['bg'] . ' ' . $b['text']; ?>">
                                        <?php echo $b['label']; ?>
                                    </span>
                                    <?php if ($tugas['status_tugas'] === 'selesai'): ?>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <span class="material-icons text-sm mr-1">check_circle</span> Selesai
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            <span class="material-icons text-sm mr-1">pending_actions</span> Pending
                                        </span>
                                    <?php endif; ?>
                                    <a href="edit_tugas.php?id=<?php echo $tugas['id_tugas']; ?>"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                        title="Edit">
                                        <span class="material-icons text-base">edit</span>
                                    </a>
                                    <a href="hapus_tugas.php?id=<?php echo $tugas['id_tugas']; ?>"
                                        onclick="return confirm('Yakin ingin menghapus tugas ini?')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition"
                                        title="Hapus">
                                        <span class="material-icons text-base">delete</span>
                                    </a>
                                    <?php if ($tugas['status_tugas'] === 'belum'): ?>
                                        <a href="tugas_selesai.php?id=<?php echo $tugas['id_tugas']; ?>"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-50 text-green-600 hover:bg-green-100 transition"
                                            title="Tandai Selesai">
                                            <span class="material-icons text-base">check</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>