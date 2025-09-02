<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet Naite - Cadastro</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎲 Bet Naite</h1>
            <p>Crie sua conta</p>
        </div>
        <div class="form-container">
            <?php
            session_start();
            require_once 'config/database.php';
            require_once 'classes/Auth.php';
            
            if (isset($_SESSION['token'])) {
                header('Location: dashboard.php');
                exit;
            }
            
            $message = '';
            $messageType = '';
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nome = $_POST['nome'] ?? '';
                $email = $_POST['email'] ?? '';
                $senha = $_POST['senha'] ?? '';
                $confirmar_senha = $_POST['confirmar_senha'] ?? '';
                $telefone = $_POST['telefone'] ?? null;
                $data_nascimento = $_POST['data_nascimento'] ?? null;
                $cpf = $_POST['cpf'] ?? null;
                
                if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
                    $message = 'Por favor, preencha todos os campos obrigatórios!';
                    $messageType = 'error';
                } elseif ($senha !== $confirmar_senha) {
                    $message = 'As senhas não coincidem!';
                    $messageType = 'error';
                } elseif (strlen($senha) < 6) {
                    $message = 'A senha deve ter pelo menos 6 caracteres!';
                    $messageType = 'error';
                } else {
                    $database = new Database();
                    $auth = new Auth($database);
                    
                    // Limpar formatação do CPF e telefone
                    $cpf = $cpf ? preg_replace('/\D/', '', $cpf) : null;
                    $telefone = $telefone ? preg_replace('/\D/', '', $telefone) : null;
                    
                    $result = $auth->register($nome, $email, $senha, $telefone, $data_nascimento, $cpf);
                    
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'error';
                    
                    if ($result['success']) {
                        // Limpar campos após sucesso
                        $_POST = [];
                    }
                }
            }
            ?>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <div class="form-group">
                    <label for="nome">Nome Completo: *</label>
                    <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email: *</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" placeholder="(11) 99999-9999" value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="data_nascimento">Data de Nascimento:</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($_POST['data_nascimento'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha: *</label>
                    <input type="password" id="senha" name="senha" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha: *</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6">
                </div>
                
                <button type="submit" class="btn">Cadastrar</button>
            </form>
            
            <div class="links">
                <p>Já tem uma conta? <a href="index.php">Faça login aqui</a></p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
