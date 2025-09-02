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
if (!$userId) {
    header('Location: users.php');
    exit;
}

// Buscar dados do usuário
$userData = $userManager->getUserById($userId);
if (!$userData) {
    header('Location: users.php');
    exit;
}

// Buscar dados da conta
$conta = $userManager->getUserAccount($userId);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet Naite - Visualizar Usuário</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1>🎲 Bet Naite</h1>
                <p>Visualizar Usuário</p>
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
            <div style="margin-bottom: 20px;">
                <a href="users.php" class="btn btn-secondary" style="width: auto;">← Voltar para Lista</a>
            </div>
            
            <div class="user-info">
                <h3>👤 Dados do Usuário #<?= $userData['id'] ?></h3>
                
                <div class="info-item">
                    <span class="info-label">Nome:</span>
                    <span class="info-value"><?= htmlspecialchars($userData['nome']) ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($userData['email']) ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Telefone:</span>
                    <span class="info-value"><?= $userData['telefone'] ? htmlspecialchars($userData['telefone']) : 'Não informado' ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Data de Nascimento:</span>
                    <span class="info-value">
                        <?= $userData['data_nascimento'] ? date('d/m/Y', strtotime($userData['data_nascimento'])) : 'Não informado' ?>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">CPF:</span>
                    <span class="info-value"><?= $userData['cpf'] ? htmlspecialchars($userData['cpf']) : 'Não informado' ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value" style="color: <?= $userData['status'] === 'ativo' ? 'green' : 'red' ?>;">
                        <?= ucfirst($userData['status']) ?>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Cadastrado em:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($userData['created_at'])) ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Última atualização:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($userData['updated_at'])) ?></span>
                </div>
            </div>
            
            <?php if ($conta): ?>
            <div class="user-info">
                <h3>💰 Informações da Conta</h3>
                
                <div class="info-item">
                    <span class="info-label">Saldo:</span>
                    <span class="info-value">R$ <?= number_format($conta['saldo'], 2, ',', '.') ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Conta criada em:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($conta['created_at'])) ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Última movimentação:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($conta['updated_at'])) ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($userData['id'] != $user['id']): ?>
            <div class="user-info">
                <h3>⚠️ Ações Administrativas</h3>
                <div style="margin-top: 15px;">
                    <button onclick="confirmDelete(<?= $userData['id'] ?>, '<?= htmlspecialchars($userData['nome']) ?>')" 
                            class="btn btn-danger" style="width: auto;">Excluir Usuário</button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
