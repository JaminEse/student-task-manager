<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
include 'includes/functions.php';  // Include the functions file for flash messages

// Redirect to login if user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$filter = $_GET['filter'] ?? 'all';
?>

<h2>Your Tasks</h2>

<?php
// Display any flash messages at the top
display_flash_messages();
?>

<!-- Filter Form -->
<form method="GET" action="view_tasks.php" class="form-row">
    <label for="filter">Filter:</label>
    <select name="filter" id="filter">
        <option value="all" <?php if ($filter === 'all') echo 'selected'; ?>>All</option>
        <option value="pending" <?php if ($filter === 'pending') echo 'selected'; ?>>Pending</option>
        <option value="done" <?php if ($filter === 'done') echo 'selected'; ?>>Done</option>
        <option value="overdue" <?php if ($filter === 'overdue') echo 'selected'; ?>>Overdue</option>
    </select>
    <button type="submit" class="button">Apply</button>
</form>

<?php
// Build the SQL query safely
$sql = "SELECT id, title, due_date, created_at, status FROM tasks WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($filter === 'pending') {
    $sql .= " AND status = 'pending'";
} elseif ($filter === 'done') {
    $sql .= " AND status = 'done'";
} elseif ($filter === 'overdue') {
    $sql .= " AND due_date < CURDATE() AND status != 'done'";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Task</th>
            <th>Due Date</th>
            <th>Created</th>
            <th>Status</th>
            <th>Update</th>
            <th>Delete</th>
            <th>Edit</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["title"]); ?></td>
                <td>
                    <?php
                    $today = date('Y-m-d');
                    $due = $row["due_date"];
                    if ($due && $due < $today && $row["status"] !== "done") {
                        echo "<span class='overdue'>" . htmlspecialchars($due) . " (Overdue)</span>";
                    } else {
                        echo $due ? htmlspecialchars($due) : "-";
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($row["created_at"]); ?></td>
                <td>
                    <?php
                    echo $row["status"] === "done"
                        ? "<span class='status-done'>Done</span>"
                        : "<span class='status-pending'>Pending</span>";
                    ?>
                </td>
                <td>
                    <a class="button" href="toggle_status.php?id=<?php echo $row['id']; ?>">
                        <?php echo $row["status"] === "pending" ? "Mark as Done" : "Mark as Pending"; ?>
                    </a>
                </td>
                <td>
                    <a class="button button--danger" href="delete_task.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                </td>
                <td>
                    <a class="button" href="edit_task.php?id=<?php echo $row['id']; ?>">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>You haven't added any tasks yet.</p>
<?php endif; ?>

<!-- Action Buttons -->
<p class="mt-2">
    <a class="button" href="add_task.php">Add New Task</a>
    <a class="button" href="welcome.php">Back to Welcome</a>
</p>

<?php include 'includes/footer.php'; ?>
