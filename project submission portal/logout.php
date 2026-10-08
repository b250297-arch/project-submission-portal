<?php
require_once 'config.php';

unset($_SESSION['admin_logged_in']);
session_destroy();
header('Location: admin_login.php');
exit;
