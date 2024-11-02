<?php
$mysqli = new mysqli("mysql", "user", "password", "user_management");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Verificar se o ID do usuário foi passado na URL
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Deletar o usuário do banco de dados
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Redirecionar para a página principal com a mensagem de exclusão
    header("Location: index.php?message=Usuário+excluído+com+sucesso");
    exit;
} else {
    echo "ID de usuário não fornecido.";
    exit;
}
