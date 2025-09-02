<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Auth.php';

if (isset($_SESSION['token'])) {
    $database = new Database();
    $auth = new Auth($database);
    $auth->logout($_SESSION['token']);
}

// Limpar sessão
session_destroy();

header('Location: index.php');
exit;
?>
