<?php
require_once 'config/auth.php';

$auth->logout();
header('Location: index.php?message=logout_success');
exit;
?>