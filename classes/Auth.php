<?php
require_once 'classes/SimpleJWT.php';

class Auth {
    private $db;
    private $secret_key = 'bet_naite_secret_key_2025';
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function register($nome, $email, $senha, $telefone = null, $data_nascimento = null, $cpf = null) {
        try {
            // Verificar se email já existe
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email já cadastrado!'];
            }
            
            // Verificar se CPF já existe
            if ($cpf) {
                $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE cpf = ?");
                $stmt->execute([$cpf]);
                
                if ($stmt->rowCount() > 0) {
                    return ['success' => false, 'message' => 'CPF já cadastrado!'];
                }
            }
            
            // Hash da senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            // Inserir usuário
            $stmt = $this->db->prepare("INSERT INTO usuarios (nome, email, senha, telefone, data_nascimento, cpf) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha_hash, $telefone, $data_nascimento, $cpf]);
            
            $user_id = $this->db->lastInsertId();
            
            // Criar conta para o usuário
            $stmt = $this->db->prepare("INSERT INTO contas (user_id, saldo) VALUES (?, 0.00)");
            $stmt->execute([$user_id]);
            
            return ['success' => true, 'message' => 'Usuário cadastrado com sucesso!'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $e->getMessage()];
        }
    }
    
    public function login($email, $senha) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND status = 'ativo'");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuário não encontrado!'];
            }
            
            $user = $stmt->fetch();
            
            if (!password_verify($senha, $user['senha'])) {
                return ['success' => false, 'message' => 'Senha incorreta!'];
            }
            
            // Gerar token JWT
            $payload = [
                'iss' => 'bet_naite',
                'sub' => $user['id'],
                'iat' => time(),
                'exp' => time() + (24 * 60 * 60) // 24 horas
            ];
            
            $token = SimpleJWT::encode($payload, $this->secret_key);
            
            // Salvar token no banco
            $stmt = $this->db->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))");
            $stmt->execute([$user['id'], $token, $payload['exp']]);
            
            return [
                'success' => true, 
                'token' => $token, 
                'user' => [
                    'id' => $user['id'],
                    'nome' => $user['nome'],
                    'email' => $user['email']
                ]
            ];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao fazer login: ' . $e->getMessage()];
        }
    }
    
    public function logout($token) {
        try {
            $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE token = ?");
            $stmt->execute([$token]);
            return ['success' => true, 'message' => 'Logout realizado com sucesso!'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao fazer logout'];
        }
    }
    
    public function validateToken($token) {
        try {
            $decoded = SimpleJWT::decode($token, $this->secret_key);
            
            // Verificar se token existe no banco e não expirou
            $stmt = $this->db->prepare("SELECT * FROM user_tokens WHERE token = ? AND expires_at > NOW()");
            $stmt->execute([$token]);
            
            if ($stmt->rowCount() == 0) {
                return false;
            }
            
            // Buscar dados do usuário
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ? AND status = 'ativo'");
            $stmt->execute([$decoded->sub]);
            
            if ($stmt->rowCount() == 0) {
                return false;
            }
            
            return $stmt->fetch();
            
        } catch(Exception $e) {
            return false;
        }
    }
    
    public function updateUser($id, $nome, $telefone = null, $data_nascimento = null, $cpf = null) {
        try {
            // Verificar se CPF já existe para outro usuário
            if ($cpf) {
                $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE cpf = ? AND id != ?");
                $stmt->execute([$cpf, $id]);
                
                if ($stmt->rowCount() > 0) {
                    return ['success' => false, 'message' => 'CPF já cadastrado para outro usuário!'];
                }
            }
            
            $stmt = $this->db->prepare("UPDATE usuarios SET nome = ?, telefone = ?, data_nascimento = ?, cpf = ? WHERE id = ?");
            $stmt->execute([$nome, $telefone, $data_nascimento, $cpf, $id]);
            
            return ['success' => true, 'message' => 'Dados atualizados com sucesso!'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar dados: ' . $e->getMessage()];
        }
    }
    
    public function changePassword($id, $senha_atual, $senha_nova) {
        try {
            // Verificar senha atual
            $stmt = $this->db->prepare("SELECT senha FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            
            if (!password_verify($senha_atual, $user['senha'])) {
                return ['success' => false, 'message' => 'Senha atual incorreta!'];
            }
            
            // Atualizar senha
            $senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt->execute([$senha_hash, $id]);
            
            // Remover todos os tokens do usuário (forçar novo login)
            $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE user_id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'Senha alterada com sucesso!'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao alterar senha: ' . $e->getMessage()];
        }
    }
}
?>
