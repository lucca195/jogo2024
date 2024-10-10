<?php
session_start();
include 'db.php';
include 'mercado_pago.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $withdraw_amount = $_POST['amount'];
    
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user['balance'] >= $withdraw_amount) {
        $new_balance = $user['balance'] - $withdraw_amount;
        
        // Atualizar saldo no banco
        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        if ($stmt->execute([$new_balance, $_SESSION['user_id']])) {
            echo "Saque realizado com sucesso!";
        }
    } else {
        echo "Saldo insuficiente para o saque.";
    }
}
?>

<h1>Saque</h1>
<form method="POST">
    <input type="number" name="amount" placeholder="Valor para saque" required>
    <button type="submit">Sacar</button>
</form>
