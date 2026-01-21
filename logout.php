<?php
session_start();
session_unset();        // Clear all login info
session_destroy();      // End session
header("Location: login.php");
exit;