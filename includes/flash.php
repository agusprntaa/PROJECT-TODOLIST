<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Fungsi untuk set pesan flash
function set_flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Fungsi untuk menampilkan pesan flash
function show_flash() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $msg = htmlspecialchars($_SESSION['flash']['message']);
        // HTML flash message dengan animasi minimalis elegan
        echo '<div id="flash-message"
            class="fixed top-6 left-1/2 transform  z-50 px-6 py-3 rounded-lg shadow-lg text-white text-sm font-semibold '
            . ($type === 'success' ? 'bg-[#328E6E]' : 'bg-red-500') . '"
            style="min-width:200px; max-width:90vw; transition: opacity 0.5s;">'
            . $msg .
        '</div>';
        // CSS animasi flash message (hanya sekali di halaman)
        echo '<style>
        #flash-message {
            opacity: 1;
            animation: flash-in 0.5s;
        }
        @keyframes flash-in {
            from { opacity: 0; transform: translateY(-20px) scale(0.95);}
            to   { opacity: 1; transform: translateY(0) scale(1);}
        }
        /* Animasi masuk turun dari atas */
        .slide-down {
          opacity: 0;
          transform: translateY(-30px);
          transition: opacity 0.4s cubic-bezier(.4,0,.2,1), transform 0.4s cubic-bezier(.4,0,.2,1);
        }
        .slide-down.show {
          opacity: 1;
          transform: translateY(0);
        }
        </style>';
        // JS auto-hide flash message
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const flash = document.getElementById("flash-message");
            if (flash) {
                setTimeout(() => flash.classList.add("show"), 50); // trigger animasi masuk
                setTimeout(() => {
                    flash.style.opacity = 0;
                    setTimeout(() => flash.remove(), 400);
                }, 2500);
            }
        });
        </script>';
        unset($_SESSION['flash']);
    }
}
?>