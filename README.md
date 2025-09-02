# Bet Naite - Sistema de Gestão de Apostas

## Descrição
Sistema completo para gestão de apostas online desenvolvido em PHP, HTML, CSS e JavaScript com banco de dados MySQL.

## Funcionalidades Implementadas

### 1. Gestão de Usuários ✅
- ✅ Criar novo usuário (auto-cadastro)
- ✅ Listar usuários
- ✅ Visualizar dados de um usuário
- ✅ Excluir usuário
- ✅ Entrar no sistema (login)
- ✅ Sair do sistema (logout)
- ✅ Alterar dados cadastrais
- ✅ Alterar senha

### 2. Sistema de Autenticação ✅
- ✅ Senhas criptografadas (password_hash)
- ✅ Tokens JWT para sessões
- ✅ Validação de tokens
- ✅ Controle de acesso às páginas

### 3. Interface Responsiva ✅
- ✅ Design moderno e responsivo
- ✅ Formulários validados
- ✅ Máscaras para CPF e telefone
- ✅ Alertas e mensagens de feedback

## Estrutura do Projeto

```
bet-naite/
├── assets/
│   ├── css/
│   │   └── style.css          # Estilos CSS responsivos
│   └── js/
│       └── script.js          # JavaScript para validações e formatação
├── classes/
│   ├── Auth.php               # Classe de autenticação
│   ├── UserManager.php        # Gerenciamento de usuários
│   └── SimpleJWT.php          # Implementação JWT personalizada
├── config/
│   └── database.php           # Configuração do banco de dados
├── database/
│   └── schema.sql             # Script SQL para criação das tabelas
├── index.php                  # Página de login
├── register.php               # Página de cadastro
├── dashboard.php              # Dashboard principal
├── profile.php                # Edição de perfil
├── change_password.php        # Alteração de senha
├── users.php                  # Lista de usuários
├── view_user.php              # Visualização de usuário
├── delete_user.php            # Exclusão de usuário
├── logout.php                 # Logout
├── composer.json              # Dependências PHP
└── README.md                  # Este arquivo
```

## Instalação

### Pré-requisitos
- XAMPP ou similar (Apache + MySQL + PHP 7.4+)
- Navegador web

### Passos para instalação:

1. **Configurar o banco de dados**
   - Inicie o XAMPP (Apache e MySQL)
   - Acesse phpMyAdmin (http://localhost/phpmyadmin)
   - Execute o script SQL em `database/schema.sql`

2. **Configurar conexão com banco**
   - Edite `config/database.php` se necessário
   - Padrão: host=localhost, user=root, password=vazio

3. **Acessar a aplicação**
   - Abra o navegador e acesse: `http://localhost/bet-naite`

## Credenciais Padrão

**Administrador:**
- Email: admin@betnaite.com
- Senha: admin123

## Como Usar

### Para novos usuários:
1. Acesse a página inicial
2. Clique em "Cadastre-se aqui"
3. Preencha o formulário de cadastro
4. Faça login com suas credenciais

### Funcionalidades disponíveis:
- **Dashboard**: Visão geral da conta e informações do usuário
- **Meu Perfil**: Editar dados pessoais
- **Alterar Senha**: Trocar a senha da conta
- **Gerenciar Usuários**: Ver lista de todos os usuários (visualizar e excluir)

## Recursos de Segurança

- **Criptografia de senhas**: Usando `password_hash()` do PHP
- **Tokens JWT**: Para controle de sessões seguras
- **Validação de dados**: Frontend e backend
- **Proteção CSRF**: Tokens de sessão
- **SQL Injection**: Prepared statements
- **XSS Protection**: `htmlspecialchars()` em todas as saídas

## Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, CSS3, JavaScript
- **Banco de dados**: MySQL 5.7+
- **Autenticação**: JWT (implementação personalizada)
- **Design**: CSS Grid, Flexbox, Responsivo

## Estrutura do Banco de Dados

### Tabela `usuarios`
- id, nome, email, senha, telefone, data_nascimento, cpf, status, created_at, updated_at

### Tabela `user_tokens`
- id, user_id, token, expires_at, created_at

### Tabela `contas`
- id, user_id, saldo, created_at, updated_at

### Tabela `lancamentos`
- id, conta_id, tipo, valor, descricao, created_at

## Próximos Desenvolvimentos

As próximas funcionalidades a serem implementadas incluem:

2. **Manter conta**
   - Depositar
   - Sacar
   - Visualizar extrato

3. **Manter lançamentos**
   - Criar lançamento
   - Listar lançamentos
   - Excluir lançamento

4. **Manter jogos**
   - CRUD completo de jogos

5. **Manter apostas**
   - Sistema de apostas completo