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

// Ambil data user dari database
$stmt = $koneksi->prepare("SELECT nama, email, username, avatar FROM users WHERE id_user = ?");
$stmt->bind_param('i', $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Cek apakah user ditemukan
if (!$user) {
    set_flash('User tidak ditemukan.', 'error');
    header('Location: dashboard.php');
    exit;
}

// Proses upload avatar
if (isset($_POST['upload_avatar']) && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    // Validasi ekstensi dan ukuran file
    if (!in_array($ext, $allowed)) {
        set_flash('Format file tidak didukung. Hanya jpg, jpeg, png, gif.', 'error');
    } elseif ($_FILES['avatar']['size'] > $maxSize) {
        set_flash('Ukuran file maksimal 2MB.', 'error');
    } else {
        $filename = 'avatar_' . $id_user . '_' . time() . '.' . $ext;
        $target = 'uploads/' . $filename;
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
            // Hapus avatar lama jika ada
            if (!empty($user['avatar']) && file_exists('uploads/' . $user['avatar'])) {
                unlink('uploads/' . $user['avatar']);
            }
            // Simpan nama file ke database
            $stmt = $koneksi->prepare("UPDATE users SET avatar=? WHERE id_user=?");
            $stmt->bind_param('si', $filename, $id_user);
            $stmt->execute();
            set_flash('Avatar berhasil diupload!', 'success');
            header('Location: profil.php');
            exit;
        } else {
            set_flash('Gagal upload avatar.', 'error');
        }
    }
}

// Ambil ulang data user setelah upload avatar
$stmt = $koneksi->prepare("SELECT nama, email, username, avatar FROM users WHERE id_user = ?");
$stmt->bind_param('i', $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$avatar = !empty($user['avatar']) && file_exists('uploads/' . $user['avatar'])
    ? 'uploads/' . $user['avatar']
    : 'styles/assets/default-avatar.png'; // default avatar
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Member</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    <main class="flex-1 px-2 md:px-10 py-8 bg-gray-50">
        <div class="max-w-lg mx-auto bg-white rounded-2xl shadow p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="material-icons text-base text-[#328E6E]">person</span>
                Profil Member
            </h2>
            <?php show_flash(); ?>
            <form method="POST" enctype="multipart/form-data" class="mb-6">
                <label class="block text-gray-700 mb-1 font-semibold">Avatar</label>
                <input type="file" name="avatar" accept="image/*" class="mb-2">
                <button type="submit" name="upload_avatar" class="bg-[#328E6E] text-white px-3 py-1 rounded hover:bg-[#256d52] text-sm">Upload</button>
            </form>
            <div class="w-full h-64 rounded-2xl overflow-hidden relative mb-4">
                <img src="<?php echo $avatar; ?>" alt="Avatar"
                    class="w-full h-full object-cover object-[center_30%]">
                <div class="absolute inset-0 rounded-2xl pointer-events-none"
                    style="background: linear-gradient(to bottom, rgba(255,255,255,0) 60%, rgba(255,255,255,0.95) 100%);">
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    
                    <span class="text-gray-500 text-xs">Nama:</span>
                    <div class="font-semibold text-lg"><?php echo htmlspecialchars($user['nama']); ?></div>
                </div>
                <div>
                    <span class="text-gray-500 text-xs">Email:</span>
                    <div><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
                <div>
                    <span class="text-gray-500 text-xs">Username:</span>
                    <div><?php echo htmlspecialchars($user['username']); ?></div>
                </div>
            </div>
            <div class="mt-8">
                <a href="dashboard.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Kembali ke Dashboard</a>
            </div>
        </div>
    </main>
</body>
</html>