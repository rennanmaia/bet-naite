<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Auth.php';
require_once 'classes/UserManager.php';

// Verificar se usuário está logado
if (!isset($_SESSION['token'])) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$auth = new Auth($database);
$userManager = new UserManager($database);

// Validar token
$user = $auth->validateToken($_SESSION['token']);
if (!$user) {
    unset($_SESSION['token']);
    unset($_SESSION['user']);
    header('Location: index.php');
    exit;
}

// Verificar se ID foi fornecido
$userId = $_GET['id'] ?? null;
if (!$userId || $userId == $user['id']) {
    header('Location: users.php');
    exit;
}

// Verificar se usuário existe
$userData = $userManager->getUserById($userId);
if (!$userData) {
    header('Location: users.php');
    exit;
}

// Executar exclusão
$result = $userManager->deleteUser($userId);

// Redirecionar com mensagem
session_start();
$_SESSION['message'] = $result['message'];
$_SESSION['messageType'] = $result['success'] ? 'success' : 'error';

header('Location: users.php');
exit;
?>
