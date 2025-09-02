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

// Buscar todos os usuários
$users = $userManager->getAllUsers();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet Naite - Gerenciar Usuários</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1>🎲 Bet Naite</h1>
                <p>Gerenciar Usuários</p>
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
            <?php
            // Exibir mensagens da sessão
            if (isset($_SESSION['message'])) {
                $message = $_SESSION['message'];
                $messageType = $_SESSION['messageType'] ?? 'error';
                unset($_SESSION['message']);
                unset($_SESSION['messageType']);
                echo "<div class='alert alert-{$messageType}'>" . htmlspecialchars($message) . "</div>";
            }
            ?>
            
            <div class="user-info">
                <h3>👥 Lista de Usuários (<?= count($users) ?> usuários)</h3>
                
                <?php if (empty($users)): ?>
                    <p>Nenhum usuário encontrado.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Data Nasc.</th>
                                    <th>Status</th>
                                    <th>Cadastrado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $userItem): ?>
                                    <tr>
                                        <td><?= $userItem['id'] ?></td>
                                        <td><?= htmlspecialchars($userItem['nome']) ?></td>
                                        <td><?= htmlspecialchars($userItem['email']) ?></td>
                                        <td><?= $userItem['telefone'] ? htmlspecialchars($userItem['telefone']) : '-' ?></td>
                                        <td>
                                            <?= $userItem['data_nascimento'] ? date('d/m/Y', strtotime($userItem['data_nascimento'])) : '-' ?>
                                        </td>
                                        <td>
                                            <span style="color: <?= $userItem['status'] === 'ativo' ? 'green' : 'red' ?>;">
                                                <?= ucfirst($userItem['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($userItem['created_at'])) ?></td>
                                        <td>
                                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                <a href="view_user.php?id=<?= $userItem['id'] ?>" class="btn btn-info btn-sm">Ver</a>
                                                <?php if ($userItem['id'] != $user['id']): ?>
                                                    <button onclick="confirmDelete(<?= $userItem['id'] ?>, '<?= htmlspecialchars($userItem['nome']) ?>')" 
                                                            class="btn btn-danger btn-sm">Excluir</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
