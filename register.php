<?php
session_start();
require_once 'includes/koneksi.php'; // Untuk koneksi ke database
require_once 'includes/auth.php'; // Untuk fungsi autentikasi
require_once 'includes/flash.php'; // Untuk fungsi flash message

//Memeriksa apakah pengguna sudah login
if (isset($_SESSION['id_user'])) {
    header('Location: dashboard.php'); // Redirect ke halaman dashboard jika sudah login
    exit;
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);

    // Validasi input
    if (!empty($nama) && !empty($email) && !empty($username) && !empty($password) && !empty($konfirmasi_password)) {
        if ($password === $konfirmasi_password) {
            // Memeriksa apakah email atau username sudah terdaftar
            $query = "SELECT * FROM users WHERE email = ? OR username = ?";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param('ss', $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                set_flash("Email atau username sudah terdaftar!", "error");
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Menyimpan data pengguna baru ke database
                $insert_query = "INSERT INTO users (nama, email, username, password) VALUES (?, ?, ?, ?)";
                $insert_stmt = $koneksi->prepare($insert_query);
                $insert_stmt->bind_param('ssss',$nama, $email, $username, $hashed_password);

                if ($insert_stmt->execute()) {
                    set_flash("Registrasi berhasil! Silakan login.", "success");
                    header('Location: login.php');
                    exit;
                } else {
                    set_flash("Terjadi kesalahan saat menyimpan data.", "error");
                }
            }
        } else {
            set_flash("Password dan konfirmasi password tidak cocok!", "error");
        }
    } else {
        set_flash("Semua kolom wajib diisi!", "error");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          nunito: ['Nunito', 'sans-serif'],
        }
      }
    }
  }
</script>
</head>
<body class="font-nunito min-h-screen bg-gradient-to-br from-[#25645d] to-[#88ffeb] flex items-center justify-center p-5">

  <div class="flex w-[900px] h-[600px] rounded-[20px] overflow-hidden shadow-2xl bg-white">  
    <!-- Kiri -->
     <div class="flex-1 flex flex-col justify-center items-center p-10 overflow-y-auto">
      <h2 class="text-xl font-semibold mb-3 text-[#328E6E]">Buat akun baru</h2>

      <?php 
      // Tampilkan flash message kecuali jika isinya timeout
      if (isset($_SESSION['flash']) && strpos($_SESSION['flash']['message'], 'Sesi Anda telah berakhir') === false) {
          show_flash();
          unset($_SESSION['flash']);
      }
      ?>

      <form method="POST" action="" class="flex flex-col gap-[15px] w-full">
        <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" required
          class="w-full px-4 py-2 text-sm rounded-lg border border-[#ccc] focus:outline-none focus:ring-2 focus:ring-[#00704a]">

        <input type="email" id="email" name="email" placeholder="Email" required
          class="w-full px-4 py-2 text-sm rounded-lg border border-[#ccc] focus:outline-none focus:ring-2 focus:ring-[#00704a]">

        <input type="text" id="username" name="username" placeholder="Username" required
          class="w-full px-4 py-2 text-sm rounded-lg border border-[#ccc] focus:outline-none focus:ring-2 focus:ring-[#00704a]">

        <input type="password" id="password" name="password" placeholder="Password" required
          class="w-full px-4 py-2 text-sm rounded-lg border border-[#ccc] focus:outline-none focus:ring-2 focus:ring-[#00704a]">

        <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Konfirmasi Password" required
          class="w-full px-4 py-2 text-sm rounded-lg border border-[#ccc] focus:outline-none focus:ring-2 focus:ring-[#00704a]">

        <button type="submit"
          class="w-full bg-[#328E6E] text-white py-2 px-4 rounded-lg font-semibold hover:bg-[#005f3c] transition">Daftar</button>
      </form>

      <p class="mt-6 text-sm text-[#444] text-center">
        Sudah punya akun? <a href="login.php" class="text-[#328E6E] font-bold">Login di sini</a>
      </p>
    </div>
  

    <!-- Kanan -->
    <div class="flex-1 relative bg-[linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url('styles/assets/img-register.png')] bg-cover bg-center p-10 text-center flex flex-col justify-center">
      <img src="styles/assets/logo-catatin.png" alt="Logo CatatIn" class="absolute -top-7 left-0 w-36 h-auto" />
      <h1 class="text-2xl mb-px-10px text-white font-bold">
        Daftarkan akun Anda di <span class="text-[#00ffd1]">CatatIn</span>
      </h1>
      <p class="text-sm text-white mb-8">Bikin Hidup Lebih Teratur, Bareng CatatIn.</p>
    </div>
  </div>
</body>
</html>