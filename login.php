<?php
session_start();
require_once 'includes/koneksi.php';
require_once 'includes/auth.php';
require_once 'includes/flash.php';

if (isset($_SESSION['id_user'])) {
  header('Location: dashboard.php');
  exit();
}

$error = "";

// Set flash khusus timeout hanya di login.php
if (isset($_GET['timeout'])) {
  set_flash('Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.', 'error');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (empty($email) || empty($password)) {
    set_flash('Email atau password tidak boleh kosong!', 'error');
  } else {
    if (login($email, $password)) {
      set_flash('Login Berhasil!', 'success');
      header('Location: dashboard.php');
      exit;
    } else {
      set_flash('Email atau password salah!', 'error');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login CatatIn</title>
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

<body
  class="font-nunito min-h-screen bg-gradient-to-br from-[#25645d] to-[#88ffeb] flex items-center justify-center p-5">

  <div class="flex w-[900px] h-[500px] rounded-[20px] overflow-hidden shadow-2xl bg-white">
    <!-- Kiri -->
    <div
      class="flex-1 relative bg-[linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url('styles/assets/img-login.png')] bg-cover bg-center p-10 text-center flex flex-col justify-center">
      <img src="styles/assets/logo-catatin.png" alt="Logo CatatIn" class="absolute -top-7 left-0 w-36 h-auto" />
      <h1 class="text-2xl mb-px-10px text-white font-bold">
        Selamat Datang di <span class="text-[#00ffd1]">CatatIn</span>
      </h1>
      <p class="text-sm text-white mb-8">Bikin Hidup Lebih Teratur, Bareng CatatIn.</p>
    </div>


    <!-- Kanan -->
    <div class="flex-1 flex flex-col justify-center items-center p-10">
      <h2 class="text-xl font-semibold mb-3 text-[#328E6E]">Masuk ke akun anda</h2>
      <?php show_flash(); // Menampilkan flash message ?>
      <form method="POST" action="" class="flex flex-col gap-4 w-full max-w-[300px] mt-1">
        <input type="email" name="email" placeholder="Email"
          class="w-full px-4 py-2 text-sm rounded-lg border border-[#ccc] focus:outline-none focus:ring-2 focus:ring-[#328E6E]">
        <input type="password" name="password" placeholder="Password"
          class="w-full px-4 py-2 text-sm rounded-lg border border-[#ccc] focus:outline-none focus:ring-2 focus:ring-[#328E6E]">

        <button type="submit"
          class="w-full bg-[#328E6E] text-white py-2 px-4 rounded-lg font-semibold hover:bg-[#005f3c] transition">
          Login
        </button>

        <div class="text-center text-sm text-[#888] mt-1">Or Continue with</div>

        <div class="flex flex-col gap-3 -mt-1">
          <button type="button"
            class="flex items-center justify-center gap-2 w-full px-4 py-2 border rounded-lg hover:bg-gray-100 transition">
            <img src="styles/assets/google.png" class="w-5 h-5" alt="Google"> <span class="text-sm">Google</span>
          </button>

          <button type="button"
            class="flex items-center justify-center gap-1 w-full px-4 py-0.5 border rounded-lg hover:bg-gray-100 transition">
            <img src="styles/assets/facebook.png" class="w-8 h-8" alt="Facebook"> <span class="text-sm">Facebook</span>
          </button>
        </div>

        <p class="text-sm text-[#444] text-center mt-2">
          Belum memiliki akun? <a href="register.php" class="text-[#328E6E] font-bold">Sign Up</a>
        </p>
      </form>
    </div>

  </div>
  </div>
  </div>
</body>

</html>