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

// Buscar dados da conta
$conta = $userManager->getUserAccount($user['id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet Naite - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1>🎲 Bet Naite</h1>
                <p>Bem-vindo, <?= htmlspecialchars($user['nome']) ?>!</p>
            </div>
            <div>
                <a href="logout.php" class="btn btn-secondary">Sair</a>
            </div>
        </div>
        
        <div class="dashboard-nav">
            <div class="nav-buttons">
                <a href="dashboard.php" class="btn">Dashboard</a>
                <a href="profile.php" class="btn">Meu Perfil</a>
                <a href="users.php" class="btn">Gerenciar Usuários</a>
                <a href="change_password.php" class="btn">Alterar Senha</a>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="user-info">
                <h3>📊 Informações da Conta</h3>
                <div class="info-item">
                    <span class="info-label">Nome:</span>
                    <span class="info-value"><?= htmlspecialchars($user['nome']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telefone:</span>
                    <span class="info-value"><?= $user['telefone'] ? htmlspecialchars($user['telefone']) : 'Não informado' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Nascimento:</span>
                    <span class="info-value"><?= $user['data_nascimento'] ? date('d/m/Y', strtotime($user['data_nascimento'])) : 'Não informado' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">CPF:</span>
                    <span class="info-value"><?= $user['cpf'] ? htmlspecialchars($user['cpf']) : 'Não informado' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Saldo:</span>
                    <span class="info-value">R$ <?= number_format($conta['saldo'], 2, ',', '.') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Membro desde:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
            
            <div class="user-info">
                <h3>🎯 Ações Rápidas</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">
                    <a href="profile.php" class="btn" style="width: auto;">Editar Perfil</a>
                    <a href="change_password.php" class="btn" style="width: auto;">Alterar Senha</a>
                    <a href="users.php" class="btn" style="width: auto;">Ver Usuários</a>
                </div>
            </div>
            
            <div class="user-info">
                <h3>📈 Estatísticas</h3>
                <div class="info-item">
                    <span class="info-label">Status da Conta:</span>
                    <span class="info-value" style="color: <?= $user['status'] === 'ativo' ? 'green' : 'red' ?>;">
                        <?= ucfirst($user['status']) ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Última Atualização:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
