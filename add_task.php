<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];
    $user_id = $_SESSION['user_id'];

    if ($title === '') {
        add_flash_message("Task title cannot be empty.", "danger");
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, due_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $due_date);
        if ($stmt->execute()) {
            add_flash_message("Task added successfully.", "success");
            header("Location: view_tasks.php");
            exit;
        } else {
            add_flash_message("Failed to add task.", "danger");
        }
    }
}
?>

<h2>Add New Task</h2>

<?php display_flash_messages(); ?>


<form method="POST" action="add_task.php">
    <label>Task Title</label>
    <input type="text" name="title" required>
    <label>Due Date</label>
    <input type="date" name="due_date">
    <button type="submit" class="button">Add Task</button>
</form>

<br>

<a class="button" href="view_tasks.php">Back to Tasks</a>

<?php include 'includes/footer.php'; ?>
