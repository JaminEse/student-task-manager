<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
        add_flash_message("All fields are required.", "danger");
    } elseif (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        add_flash_message("Username must be 3-20 characters and contain only letters, numbers, or underscores.", "danger");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        add_flash_message("Please enter a valid email address.", "danger");
    } elseif ($password !== $confirm_password) {
        add_flash_message("Passwords do not match.", "danger");
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            add_flash_message("Email is already registered.", "danger");
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Insert username, email, and password
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                add_flash_message("Account created successfully. Please log in.", "success");
                header("Location: login.php");
                exit;
            } else {
                add_flash_message("Failed to create account.", "danger");
            }
        }
    }
}
?>

<h2>Register</h2>

<?php display_flash_messages(); ?>


    <form class="register-form" action="register.php" method="post">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit">Register</button>
    </form>


<p>Already have an account? <a href="login.php">Login here</a></p>

<?php include 'includes/footer.php'; ?>
