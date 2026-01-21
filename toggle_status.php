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

// Fetch current status
$stmt = $conn->prepare("SELECT status FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    add_flash_message("Task not found.", "danger");
    header("Location: view_tasks.php");
    exit;
}

// Toggle status
$new_status = $task['status'] === 'pending' ? 'done' : 'pending';
$stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sii", $new_status, $task_id, $_SESSION['user_id']);
$stmt->execute();

add_flash_message("Task marked as {$new_status}.", "success");
header("Location: view_tasks.php");
exit;
?>
