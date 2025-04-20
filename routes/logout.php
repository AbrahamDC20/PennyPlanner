<?php
require_once '../models/db.php'; // Use require_once to avoid duplicate inclusion

session_start();
session_unset();
session_destroy();

header('Location: ../views/login.php');
exit();
?>