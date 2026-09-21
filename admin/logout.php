<?php
require_once __DIR__ . '/../includes/functions.php';
logoutAdmin();
header('Location: login.php');
exit;
