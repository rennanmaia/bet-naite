<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet Naite - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎲 Bet Naite</h1>
            <p>Faça login em sua conta</p>
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
                $email = $_POST['email'] ?? '';
                $senha = $_POST['senha'] ?? '';
                
                if (empty($email) || empty($senha)) {
                    $message = 'Por favor, preencha todos os campos!';
                    $messageType = 'error';
                } else {
                    $database = new Database();
                    $auth = new Auth($database);
                    
                    $result = $auth->login($email, $senha);
                    
                    if ($result['success']) {
                        $_SESSION['token'] = $result['token'];
                        $_SESSION['user'] = $result['user'];
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $message = $result['message'];
                        $messageType = 'error';
                    }
                }
            }
            ?>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <button type="submit" class="btn">Entrar</button>
            </form>
            
            <div class="links">
                <p>Não tem uma conta? <a href="register.php">Cadastre-se aqui</a></p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
