<?php
session_start();
include 'db.php';
include 'mercado_pago.php';

// Função para validar a entrada do usuário
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);

    // Preparar a consulta para buscar o usuário
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verificar se o usuário existe e se a senha está correta
    if ($user && password_verify($password, $user['password_hash'])) {
        // Armazenar o ID do usuário na sessão
        $_SESSION['user_id'] = $user['id'];
        echo "Login bem-sucedido!";
        header('Location: roulette.php'); // Redirecionar para a roleta
        exit();
    } else {
        echo "Usuário ou senha incorretos."; // Mensagem de erro
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css"> <!-- Vincular o CSS -->
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="username" placeholder="Nome de usuário" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
