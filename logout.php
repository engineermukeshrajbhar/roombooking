<?php
require_once 'classes/Admin.php';

$admin = new Admin();
$admin->logout();

header('Location: admin_login.php');
exit;
?>