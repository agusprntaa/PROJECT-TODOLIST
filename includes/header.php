<?php
// Mulai sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/auth.php';

// Cek apakah user sudah login, jika tidak arahkan ke login
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pengguna dari session
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';
$email_user = isset($_SESSION['email']) ? $_SESSION['email'] : 'user@email.com';

// Ambil avatar user dari database
$avatar = 'styles/assets/default-avatar.png'; // default
if (isset($_SESSION['id_user'])) { //Cek Apakah User Sudah Login
    require_once 'includes/koneksi.php';
    $id_user = $_SESSION['id_user'];
    $stmt = $koneksi->prepare("SELECT avatar FROM users WHERE id_user=?");
    $stmt->bind_param('i', $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['avatar']) && file_exists('uploads/' . $row['avatar'])) {
            $avatar = 'uploads/' . $row['avatar'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-72 bg-white shadow-lg flex flex-col justify-between py-8 px-6">
        <div>
            <!-- Logo & App Name -->
            <div class="flex items-center mb-3">
                <span class="font-bold text-2xl text-[#328E6E]">CatatIn</span>
            </div>
            <!-- Main Menu -->
             <nav class="space-y-2 mb-8">
                <a href="profil.php" class="flex items-center py-2 px-4 rounded-lg hover:bg-indigo-50 font-medium text-gray-700 transition">
                    <span class="material-icons mr-3 text-[#328E6E]">person</span> Profile
                </a>
            <nav class="space-y-2 mb-2">
                <a href="dashboard.php" class="flex items-center py-2 px-4 rounded-lg hover:bg-indigo-50 font-medium text-gray-700 transition">
                    <span class="material-icons mr-3 text-[#328E6E]">dashboard</span> Dashboard
                </a>
            </nav>
            </nav>
            <!-- List Pekerjaan -->
            <div>
                <span class="text-gray-500 font-semibold text-xs mb-1 block">LIST PEKERJAAN</span>
                <div class="flex flex-col gap-1">
                     <a href="add_tugas.php" class="flex items-center py-2 px-4 rounded-lg hover:bg-indigo-50 font-medium text-gray-700 transition">
                    <span class="material-icons mr-3 text-[#328E6E]">check_circle</span> To-Do List
                </a>    
                </div>
            </div>
        </div>
        <!-- Akun Pengguna -->
        <div class="mt-10">
            <a href="profil.php" class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 hover:bg-indigo-100 transition">
                <div class="w-10 h-10 rounded-full bg-[#328E6E] flex items-center justify-center text-[#E1EEBC] font-bold text-lg overflow-hidden">
                    <img src="<?php echo $avatar; ?>" alt="Avatar" class="w-10 h-10 object-cover rounded-full">
                </div>
                <div>
                    <span class="block font-semibold text-[#328E6E] text-sm"><?php echo htmlspecialchars($nama_user); ?></span>
                    <span class="block text-xs text-[#537D5D]"><?php echo htmlspecialchars($email_user); ?></span>
                </div>
            </a>
            <a href="logout.php" class="flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 text-xs font-semibold transition w-full justify-center">
                <span class="material-icons text-base">logout</span>
                Logout
            </a>
        </div>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-8">
        <?php
        require_once 'includes/flash.php';
        show_flash();
        ?>