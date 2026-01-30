<?php
/**
 * User Logout
 */

session_start();
session_destroy();
session_write_close();
setcookie(session_name(), '', 0, '/');

header('Location: ../auth/login.php');
exit();
