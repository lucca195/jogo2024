<?php
session_start();
include 'db.php';
include 'mercado_pago.php';

// Função para gerar um token CSRF
function generate_csrf_token() {
    return bin2hex(random_bytes(32));
}

// Armazenar token CSRF na sessão
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generate_csrf_token();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lógica do jogo da roleta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bet = $_POST['bet'];
    $csrf_token = $_POST['csrf_token'];

    // Verificar se a aposta é um número positivo
    if (!is_numeric($bet) || $bet <= 0) {
        die("Aposta inválida! Deve ser um número positivo.");
    }

    // Verificar o token CSRF
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("CSRF token inválido!");
    }

    // Obter o saldo do usuário
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Especificar o modo de busca

    if ($user && $user['balance'] >= $bet) {
        $result = rand(0, 36); // Simulação do resultado da roleta

        // Lógica para aumentar ou diminuir o saldo
        if ($result === 7) { // Exemplo de condição de vitória (número 7)
            $new_balance = $user['balance'] + $bet;
            echo "<p>Você ganhou! O resultado foi: $result</p>";
        } else {
            $new_balance = $user['balance'] - $bet;
            echo "<p>Você perdeu! O resultado foi: $result</p>";
        }

        // Atualizar saldo no banco
        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $_SESSION['user_id']]);
    } else {
        echo "Saldo insuficiente para essa aposta.";
    }
}

// Exibir saldo e formulário de aposta
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC); // Especificar o modo de busca

if (!$user) {
    echo "Usuário não encontrado!";
    exit();
}
?>

<h1>Roleta</h1>
<p>Saldo: R$ <?php echo number_format($user['balance'], 2, ',', '.'); ?></p>

<form method="POST">
    <input type="number" name="bet" placeholder="Aposta" required min="1">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <button type="submit">Jogar</button>
</form>
