<?php
session_start();

// Destroy the session to log the user out
session_destroy();

// Redirect to the login page
header("Location: L1.html");
exit();
?>
