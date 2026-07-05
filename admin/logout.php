<?php
/**
 * Admin Logout
 * FSSAI License Verification System
 */

session_start();
session_destroy();
header('Location: login.php');
exit();
?>
