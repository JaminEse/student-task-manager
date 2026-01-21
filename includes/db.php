<?php
// If a local (non-GitHub) config exists, use it.
$localConfig = __DIR__ . "/db.local.php";
if (file_exists($localConfig)) {
    require_once $localConfig;
    return;
}

// Otherwise, use safe placeholder values (for GitHub / other machines).
$host = "localhost";
$user = "your_db_user";
$password = "your_db_password";
$dbname = "your_db_name";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database not configured. Please set up includes/db.local.php");
}
?>
