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

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email inválido!");
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $passwordHash, $email]);
        echo "Cadastro realizado com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao cadastrar: " . $e->getMessage(); // Mensagem de erro detalhada
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Nome de usuário" required>
    <input type="password" name="password" placeholder="Senha" required>
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit">Registrar</button>
</form>
