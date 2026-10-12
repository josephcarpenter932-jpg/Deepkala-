<?php
require 'backend/config.php';

// Wipe the session and go back to the homepage
session_unset();
session_destroy();

header("Location: index.php");
exit;
?>
