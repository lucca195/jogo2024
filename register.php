<?php
include 'db.php';

// Função para validar entradas
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);
    $email = validate_input($_POST['email']);

    // Valida o email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email inválido!";
        exit();
    }

    // Verifica se o nome de usuário já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        echo "Nome de usuário já está em uso!";
        exit();
    }

    // Cria o hash da senha
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Prepara e executa a inserção no banco de dados
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $passwordHash, $email])) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro ao cadastrar! Tente novamente.";
    }
}
?>

<!-- Formulário de registro -->
<form method="POST">
    <input type="text" name="username" placeholder="Nome de usuário" required>
    <input type="password" name="password" placeholder="Senha" required>
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit">Registrar</button>
</form>
