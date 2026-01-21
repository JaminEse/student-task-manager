<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email === '' || $password === '') {
        add_flash_message("Please enter both email and password.", "danger");
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $username, $hashed_password);

        if ($stmt->num_rows === 1) {
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                // Store both user_id and username in session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;

                add_flash_message("Welcome back, {$username}!", "success");
                header("Location: welcome.php");
                exit;
            } else {
                add_flash_message("Incorrect password.", "danger");
            }
        } else {
            add_flash_message("Email not found.", "danger");
        }
    }
}
?>

<h2>Login</h2>

<?php display_flash_messages(); ?>

<form method="POST" action="login.php">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit" class="button">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

<?php include 'includes/footer.php'; ?>
