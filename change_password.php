<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Auth.php';

// Verificar se usuário está logado
if (!isset($_SESSION['token'])) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$auth = new Auth($database);

// Validar token
$user = $auth->validateToken($_SESSION['token']);
if (!$user) {
    unset($_SESSION['token']);
    unset($_SESSION['user']);
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $senha_atual = $_POST['senha_atual'] ?? '';
    $senha_nova = $_POST['senha_nova'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if (empty($senha_atual) || empty($senha_nova) || empty($confirmar_senha)) {
        $message = 'Por favor, preencha todos os campos!';
        $messageType = 'error';
    } elseif ($senha_nova !== $confirmar_senha) {
        $message = 'As senhas não coincidem!';
        $messageType = 'error';
    } elseif (strlen($senha_nova) < 6) {
        $message = 'A nova senha deve ter pelo menos 6 caracteres!';
        $messageType = 'error';
    } else {
        $result = $auth->changePassword($user['id'], $senha_atual, $senha_nova);
        
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
        
        if ($result['success']) {
            // Fazer logout automático após trocar senha
            unset($_SESSION['token']);
            unset($_SESSION['user']);
            $message .= ' Você será redirecionado para fazer login novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet Naite - Alterar Senha</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1>🎲 Bet Naite</h1>
                <p>Alterar Senha</p>
            </div>
            <div>
                <?php if (isset($_SESSION['token'])): ?>
                    <a href="logout.php" class="btn btn-secondary">Sair</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($_SESSION['token'])): ?>
        <div class="dashboard-nav">
            <div class="nav-buttons">
                <a href="dashboard.php" class="btn">Dashboard</a>
                <a href="profile.php" class="btn">Meu Perfil</a>
                <a href="users.php" class="btn">Gerenciar Usuários</a>
                <a href="change_password.php" class="btn">Alterar Senha</a>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="dashboard-content">
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                    <?php if ($messageType === 'success'): ?>
                        <script>
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 3000);
                        </script>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['token'])): ?>
            <div class="user-info">
                <h3>🔐 Alterar Senha</h3>
                
                <form method="POST" id="passwordForm" style="margin-top: 20px;">
                    <div class="form-group">
                        <label for="senha_atual">Senha Atual: *</label>
                        <input type="password" id="senha_atual" name="senha_atual" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha_nova">Nova Senha: *</label>
                        <input type="password" id="senha_nova" name="senha_nova" required minlength="6">
                        <small style="color: #666; font-size: 12px;">Mínimo de 6 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Nova Senha: *</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6">
                    </div>
                    
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn" style="width: auto;">Alterar Senha</button>
                        <a href="dashboard.php" class="btn btn-secondary" style="width: auto;">Cancelar</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
                <div class="user-info">
                    <h3>🔐 Senha Alterada com Sucesso!</h3>
                    <p>Sua senha foi alterada com sucesso. Você será redirecionado para a página de login.</p>
                    <div style="margin-top: 20px;">
                        <a href="index.php" class="btn">Ir para Login</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
