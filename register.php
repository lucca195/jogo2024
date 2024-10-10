<?php
include 'db.php';

// Função para validar entradas
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);
    $email = validate_input($_POST['email']);
    $full_name = validate_input($_POST['full_name']); // Adicionado

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email inválido!";
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Agora, você também insere o `full_name`
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, full_name) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $passwordHash, $email, $full_name])) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro ao cadastrar!";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Nome de usuário" required>
    <input type="password" name="password" placeholder="Senha" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="full_name" placeholder="Nome completo" required> <!-- Adicionado -->
    <button type="submit">Registrar</button>
</form>
