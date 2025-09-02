<?php
class UserManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database->getConnection();
    }
    
    public function getAllUsers() {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, email, telefone, data_nascimento, cpf, status, created_at FROM usuarios ORDER BY nome");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, email, telefone, data_nascimento, cpf, status, created_at FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function deleteUser($id) {
        try {
            // Verificar se usuário existe
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Usuário não encontrado!'];
            }
            
            // Excluir usuário (cascata irá excluir conta, tokens e lançamentos)
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'Usuário excluído com sucesso!'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao excluir usuário: ' . $e->getMessage()];
        }
    }
    
    public function getUserAccount($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM contas WHERE user_id = ?");
            $stmt->execute([$user_id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>
