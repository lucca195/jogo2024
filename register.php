<?php
include 'db.php';

// Função para validar entradas
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Função para gerar token CSRF
function generate_csrf_token() {
    return bin2hex(random_bytes(32));
}

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generate_csrf_token();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificação do token CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token inválido!");
    }

    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);
    $email = validate_input($_POST['email']);
    $full_name = validate_input($_POST['full_name']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email inválido!";
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Agora, você também insere o `full_name`
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, full_name) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $passwordHash, $email, $full_name])) {
        // Redirecionar para a página de login
        header("Location: login.php");
        exit();
    } else {
        // Exibir mensagem de erro detalhada
        $errorInfo = $stmt->errorInfo();
        echo "Erro ao cadastrar: " . $errorInfo[2];
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Nome de usuário" required>
    <input type="password" name="password" placeholder="Senha" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="full_name" placeholder="Nome completo" required>
    <!-- Campo oculto para o token CSRF -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <button type="submit">Registrar</button>
</form>
