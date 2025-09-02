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
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? null;
    $data_nascimento = $_POST['data_nascimento'] ?? null;
    $cpf = $_POST['cpf'] ?? null;
    
    if (empty($nome)) {
        $message = 'O nome é obrigatório!';
        $messageType = 'error';
    } else {
        // Limpar formatação do CPF e telefone
        $cpf = $cpf ? preg_replace('/\D/', '', $cpf) : null;
        $telefone = $telefone ? preg_replace('/\D/', '', $telefone) : null;
        
        $result = $auth->updateUser($user['id'], $nome, $telefone, $data_nascimento, $cpf);
        
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
        
        if ($result['success']) {
            // Atualizar dados do usuário na sessão
            $user = $auth->validateToken($_SESSION['token']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet Naite - Meu Perfil</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1>🎲 Bet Naite</h1>
                <p>Meu Perfil</p>
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
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <div class="user-info">
                <h3>✏️ Editar Perfil</h3>
                
                <form method="POST" id="profileForm" style="margin-top: 20px;">
                    <div class="form-group">
                        <label for="nome">Nome Completo: *</label>
                        <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($user['nome']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" disabled value="<?= htmlspecialchars($user['email']) ?>">
                        <small style="color: #666; font-size: 12px;">O email não pode ser alterado</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" placeholder="(11) 99999-9999" value="<?= $user['telefone'] ? htmlspecialchars($user['telefone']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento:</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="<?= $user['data_nascimento'] ? htmlspecialchars($user['data_nascimento']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="<?= $user['cpf'] ? htmlspecialchars($user['cpf']) : '' ?>">
                    </div>
                    
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" class="btn" style="width: auto;">Salvar Alterações</button>
                        <a href="dashboard.php" class="btn btn-secondary" style="width: auto;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
