<?php
// functions.php handles flash messages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function add_flash_message($message, $type = 'success') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'message' => $message,
        'type' => $type
    ];
}

function display_flash_messages() {
    if (!empty($_SESSION['flash_messages'])) {
        foreach ($_SESSION['flash_messages'] as $flash) {
            $class = '';
            switch ($flash['type']) {
                case 'danger':  $class = 'flash-danger'; break;
                case 'warning': $class = 'flash-warning'; break;
                default:        $class = 'flash-success'; break;
            }

            echo "<div class='flash-message {$class}'>
                    {$flash['message']}
                    <span class='flash-close' onclick='this.parentElement.remove();'>&times;</span>
                  </div>";
        }

        unset($_SESSION['flash_messages']);

        echo "<script>
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.flash-message').forEach(flash => {
                setTimeout(() => {
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 500);
                }, 4000);
            });
        });
        </script>";
    }
}
?>
