<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
include 'includes/functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user info from session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get task stats
$sql = "SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) AS done,
            SUM(CASE WHEN due_date < CURDATE() AND status != 'done' THEN 1 ELSE 0 END) AS overdue
        FROM tasks
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
?>

<h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

<?php display_flash_messages(); ?>

<!-- Dashboard Cards -->
<div class="dashboard">
    <div class="card card-total">
        <h3>Total Tasks</h3>
        <p><?php echo $stats['total']; ?></p>
    </div>
    <div class="card card-pending">
        <h3>Pending</h3>
        <p><?php echo $stats['pending']; ?></p>
    </div>
    <div class="card card-done">
        <h3>Completed</h3>
        <p><?php echo $stats['done']; ?></p>
    </div>
    <div class="card card-overdue">
        <h3>Overdue</h3>
        <p><?php echo $stats['overdue']; ?></p>
    </div>
</div>


<!-- Action Buttons -->
<p class="mt-2">
    <a class="button" href="add_task.php">Add New Task</a>
    <a class="button" href="view_tasks.php">View Your Tasks</a>
    <a class="button button--danger" href="logout.php">Logout</a>
</p>

<?php include 'includes/footer.php'; ?>
