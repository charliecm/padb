<?php
/**
 * Logout
 */

require('../private/functions.php');

// Logout and redirect
session_start();
session_destroy();
header('Location: index.php?logged_out' );
exit;
?>
