<?php
session_start();
require_once 'includes/koneksi.php';
require_once 'includes/auth.php';
require_once 'includes/flash.php';

// SESSION TIMEOUT (15 menit = 900 detik)
$timeout = 900; // detik
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
  session_unset();
  session_destroy();
  header('Location: login.php?timeout=1');
  exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
  header('Location: login.php');
  exit;
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];

// Ambil data tugas user dengan filter (status & pencarian)
$where = "id_user = ?";
$params = [$id_user];
$types = "i";

if (!empty($_GET['status'])) { // Filter berdasarkan status
  $where .= " AND status_tugas = ?";
  $params[] = $_GET['status'];
  $types .= "s";
}

if (!empty($_GET['q'])) { // Filter berdasarkan pencarian
  $where .= " AND (nama_tugas LIKE ? OR deskripsi LIKE ?)";
  $search = '%' . $_GET['q'] . '%';
  $params[] = $search;
  $params[] = $search;
  $types .= "ss";
}

// Query untuk mengambil data tugas
$query = "SELECT * FROM tugas WHERE $where ORDER BY tanggal_deadline ASC";
$stmt = $koneksi->prepare($query);

$stmt->bind_param($types, ...$params); // Bind parameters dynamically

$stmt->execute();
$result = $stmt->get_result();
$tugas = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="bg-gray-100">
  <?php include 'includes/header.php'; ?>
  <main class="flex-1 px-2 md:px-6 py-4 bg-gray-50">
    <?php show_flash(); // flash message ?>

    <!-- Header -->
    <div class="mb-0 pb-6 border-b border-gray-200">
      <h2 class="text-3xl font-bold text-[#328E6E] mb-1">Welcome, <?php echo htmlspecialchars($nama_user); ?>!</h2>
      <p class="text-gray-500 text-base">Apa yang ingin kamu capai hari ini?</p>
    </div>

    <!-- Stats Card -->
    <div class="w-full max-w-full mt-6 mb-10">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow flex items-center gap-4 p-6 w-full">
          <span class="material-icons text-indigo-500 text-4xl bg-indigo-100 rounded-xl p-3">assignment</span>
          <div>
            <div class="text-gray-500 text-sm">Total Tugas</div>
            <div class="text-2xl font-bold"><?php echo count($tugas); ?></div>
          </div>
        </div>
        <div class="bg-white rounded-2xl shadow flex items-center gap-4 p-6 w-full">
          <span class="material-icons text-yellow-500 text-4xl bg-yellow-100 rounded-xl p-3">pending_actions</span>
          <div>
            <div class="text-gray-500 text-sm">Belum Selesai</div>
            <div class="text-2xl font-bold">
              <?php echo count(array_filter($tugas, fn($t) => $t['status_tugas'] === 'belum')); ?>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-2xl shadow flex items-center gap-4 p-6 w-full">
          <span class="material-icons text-green-500 text-4xl bg-green-100 rounded-xl p-3">check_circle</span>
          <div>
            <div class="text-gray-500 text-sm">Selesai</div>
            <div class="text-2xl font-bold">
              <?php echo count(array_filter($tugas, fn($t) => $t['status_tugas'] === 'selesai')); ?>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-2xl shadow flex items-center gap-4 p-6 w-full">
          <span class="material-icons text-red-500 text-4xl bg-red-100 rounded-xl p-3">error</span>
          <div>
            <div class="text-gray-500 text-sm">Terlambat</div>
            <div class="text-2xl font-bold">
              <?php
              $today = date('Y-m-d H:i:s');
              echo count(array_filter($tugas, function($t) use ($today) {
                return $t['status_tugas'] === 'belum' && $t['tanggal_deadline'] < $today;
              }));
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Today's Task Card -->
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-full">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h3 class="text-xl font-bold text-gray-800">Today's Task</h3>
        <a href="add_tugas.php"
           class="inline-flex items-center gap-2 bg-[#328E6E] text-white px-5 py-2 rounded-lg font-semibold shadow hover:bg-[#256d52] transition text-base">
            <span class="material-icons text-base">add</span>
            Tambah Tugas
        </a>
      </div>

      <!-- Filter Form -->
      <form method="GET" class="flex flex-col md:flex-row gap-2 mb-6">
        <input type="text" name="q" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
          placeholder="Cari tugas..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-60" />

        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
          <option value="">Semua Status</option>
          <option value="belum" <?php if (isset($_GET['status']) && $_GET['status'] == 'belum')
            echo 'selected'; ?>>Belum
            Selesai</option>
          <option value="selesai" <?php if (isset($_GET['status']) && $_GET['status'] == 'selesai')
            echo 'selected'; ?>>
            Selesai</option>
        </select>

        <button type="submit"
          class="bg-[#328E6E] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#256d52] transition">Filter</button>
        <?php if (isset($_GET['q']) || isset($_GET['status'])): ?>
          <a href="dashboard.php" class="text-sm text-gray-500 underline px-2 py-2">Reset</a>
        <?php endif; ?>
      </form>
      
      <!-- List Tugas -->
      <div class="overflow-y-auto" style="max-height: 420px;">
        <?php if (count($tugas) === 0): ?>
          <div class="text-center text-gray-400 py-10">Belum ada tugas.</div>
        <?php else: ?>
          <ul class="space-y-4 w-full" id="task-list">
            <?php foreach ($tugas as $t): ?>
              <li
                class="task-item flex items-center justify-between bg-gray-50 hover:bg-gray-100 rounded-xl px-5 py-4 shadow-sm transition w-full cursor-pointer group border border-transparent hover:border-[#328E6E]"
                title="Lihat detail tugas"
                onclick="window.location.href='detail_tugas.php?id=<?php echo $t['id_tugas']; ?>'">
                <div class="flex items-center gap-4">

                  <!-- Checkbox -->
                  <form method="POST" action="tugas_selesai.php" onsubmit="event.stopPropagation();">
                    <input type="hidden" name="id" value="<?php echo $t['id_tugas']; ?>">
                    <input type="checkbox"
                      class="form-checkbox h-5 w-5 text-[#328E6E] rounded checkbox-anim"
                      <?php if ($t['status_tugas'] === 'selesai') echo 'checked'; ?>
                      onchange="this.form.submit()"
                      onclick="event.stopPropagation();"
                    />
                  </form>

                  <div>
                    <!-- Nama tugas -->
                    <div class="font-semibold text-gray-800 group-hover:underline">
                      <?php echo htmlspecialchars($t['nama_tugas']); ?>
                    </div>
                    <!-- Deskripsi tugas (maksimal 60 karakter, jika ada) -->
                    <?php if (!empty($t['deskripsi'])): ?>
                      <div class="text-xs text-gray-500 italic mb-1">
                        <?php
                        $desc = strip_tags($t['deskripsi']);
                        echo strlen($desc) > 60 ? htmlspecialchars(substr($desc, 0, 60)) . '...' : htmlspecialchars($desc);
                        ?>
                      </div>
                    <?php endif; ?>
                      <!-- Deadline tugas -->
                    <div class="text-xs text-gray-400">Deadline: <?php echo htmlspecialchars($t['tanggal_deadline']); ?></div>
                  </div>
                </div>

                <!-- Menentukan tampilan Badge Prioritas tugas -->
                <div class="flex items-center gap-3">
                  <?php
                  $prioritas = isset($t['prioritas']) ? strtolower($t['prioritas']) : 'medium';
                  $badge = [
                    'high' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'High'],
                    'medium' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Medium'],
                    'low' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Low'],
                  ];
                  $b = $badge[$prioritas] ?? $badge['medium'];
                  ?>

                <!--menampilkan badge prioritas -->
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold <?php echo $b['bg'] . ' ' . $b['text']; ?>">
                    <?php echo $b['label']; ?>
                  </span>

                  <!-- Status tugas -->
                  <?php if ($t['status_tugas'] === 'selesai'): ?>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                      <span class="material-icons text-sm mr-1">check_circle</span> Selesai
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                      <span class="material-icons text-sm mr-1">pending_actions</span> Pending
                    </span>
                  <?php endif; ?>

                  <!-- Tombol Hapus -->
                  <a href="hapus_tugas.php?id=<?php echo $t['id_tugas']; ?>"
                    onclick="event.stopPropagation();return confirm('Yakin ingin menghapus tugas ini?')"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition"
                    title="Hapus">
                    <span class="material-icons text-base">delete</span>
                  </a>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </main>
  </div>

  <style>
.checkbox-anim {
  transition: box-shadow 0.2s, transform 0.2s;
}
.checkbox-anim:active {
  transform: scale(1.2);
  box-shadow: 0 0 0 4px #a7f3d0;
}
.checkbox-anim:checked {
  animation: pop 0.3s;
}
@keyframes pop {
  0% { transform: scale(1); }
  60% { transform: scale(1.3); }
  100% { transform: scale(1); }
}

/* Animasi masuk (fade in + slide) */
.task-item {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.4s, transform 0.4s;
}
.task-item.show {
  opacity: 1;
  transform: translateY(0);
}
/* Animasi keluar (fade out) */
.task-item.hide {
  opacity: 0 !important;
  transform: translateY(-20px) !important;
  transition: opacity 0.3s, transform 0.3s;
}
</style>

<script>
document.querySelectorAll('.checkbox-anim').forEach(cb => {
  cb.addEventListener('change', function() {
    this.classList.add('checked-animate');
    setTimeout(() => this.classList.remove('checked-animate'), 300);
  });
});

// Animasi masuk untuk semua task-item saat halaman dimuat
window.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.task-item').forEach((el, i) => {
    setTimeout(() => el.classList.add('show'), 60 * i);
  });
});

// (Opsional) Animasi keluar saat klik tombol hapus
document.querySelectorAll('.task-item a[title="Hapus"]').forEach(btn => {
  btn.addEventListener('click', function(e) {
    const li = this.closest('.task-item');
    if (li) {
      li.classList.add('hide');
      setTimeout(() => {
        // Setelah animasi keluar, biarkan proses hapus berjalan (form submit/redirect)
      }, 300);
    }
  });
});
</script>
</body>

</html>