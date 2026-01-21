<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$task_id = $_GET['id'] ?? null;
if (!$task_id) {
    add_flash_message("Invalid task ID.", "danger");
    header("Location: view_tasks.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
if ($stmt->execute()) {
    add_flash_message("Task deleted successfully.", "success");
} else {
    add_flash_message("Failed to delete task.", "danger");
}

header("Location: view_tasks.php");
exit;
?>
