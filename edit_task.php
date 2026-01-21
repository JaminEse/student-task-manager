<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
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

// Fetch task
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    add_flash_message("Task not found.", "danger");
    header("Location: view_tasks.php");
    exit;
}

// Update task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];

    if ($title === '') {
        add_flash_message("Task title cannot be empty.", "danger");
    } else {
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, due_date = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $title, $due_date, $task_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            add_flash_message("Task updated successfully.", "success");
            header("Location: view_tasks.php");
            exit;
        } else {
            add_flash_message("Failed to update task.", "danger");
        }
    }
}
?>

<h2>Edit Task</h2>
<?php display_flash_messages(); ?>

<form method="POST" action="">
    <label>Task Title</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
    <label>Due Date</label>
    <input type="date" name="due_date" value="<?php echo $task['due_date']; ?>">
    <button type="submit" class="button">Update Task</button>
</form>

<a class="button" href="view_tasks.php">Back to Tasks</a>
<?php include 'includes/footer.php'; ?>
