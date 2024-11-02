<?php
$mysqli = new mysqli("mysql", "user", "password", "user_management");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$errors = [];

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $data_nascimento = $_POST['data_nascimento'];
        $foto = $_POST['current_foto'];

        // Validação do Nome
        if (strlen($nome) < 3) {
            $errors[] = "Nome precisa ter ao menos 3 caracteres.";
        }

        // Validação do Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email inválido.";
        } else {
            // Verificar se o email é único (exceto para o usuário atual)
            $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "Este email já está em uso.";
            }
        }

        // Validação da Data de Nascimento
        $dob = new DateTime($data_nascimento);
        $now = new DateTime();
        $age = $now->diff($dob)->y;
        if ($age < 18) {
            $errors[] = "Usuário precisa ser maior de 18 anos.";
        }

        // Validação da Foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fotoTmpPath = $_FILES['foto']['tmp_name'];
            $fotoSize = $_FILES['foto']['size'];
            $fotoExtension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $fotoInfo = getimagesize($fotoTmpPath);

            if ($fotoSize > 200000) {
                $errors[] = "A foto deve ter no máximo 200 KB.";
            }

            if ($fotoInfo[0] > 700 || $fotoInfo[1] > 1080) {
                $errors[] = "A foto deve ter no máximo 700x1080 pixels.";
            }

            if (!in_array($fotoExtension, ['jpg', 'jpeg'])) {
                $errors[] = "A foto deve estar em formato .jpg ou .jpeg.";
            }

            if (empty($errors)) {
                $fotoName = uniqid() . ".jpg";
                $fotoUploadPath = "uploads/" . $fotoName;
                move_uploaded_file($fotoTmpPath, $fotoUploadPath);
                $foto = $fotoName;
            }
        }

        // Atualizar dados se não houver erros
        if (empty($errors)) {
            $stmt = $mysqli->prepare("UPDATE users SET nome = ?, email = ?, data_nascimento = ?, foto = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nome, $email, $data_nascimento, $foto, $id);
            $stmt->execute();
            header("Location: index.php?message=Usuário+atualizado+com+sucesso");
            exit;
        }
    }

    // Buscar dados do usuário
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    echo "ID de usuário não fornecido.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuário</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .menu { margin-bottom: 20px; }
        .menu a { margin-right: 10px; text-decoration: none; color: #007BFF; }
        .menu a:hover { text-decoration: underline; }
        .form-container { max-width: 400px; margin: 0 auto; }
        .error-message { color: #721c24; background-color: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], input[type="date"], input[type="file"] { width: 100%; padding: 8px; }
        button { padding: 10px 20px; background-color: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        img.thumbnail { width: 100px; margin-top: 10px; }
    </style>
</head>
<body>

    <!-- Menu -->
    <div class="menu">
        <a href="index.php">Início</a>
        <a href="add_user.php">Adicionar Usuário</a>
    </div>

    <h1>Editar Usuário</h1>

    <!-- Mensagens de erro -->
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" name="data_nascimento" id="data_nascimento" value="<?= htmlspecialchars($user['data_nascimento']) ?>" required>
            </div>
            <div class="form-group">
                <label>Foto Atual:</label>
                <?php if ($user['foto']): ?>
                    <img src="uploads/<?= htmlspecialchars($user['foto']) ?>" class="thumbnail"><br>
                <?php else: ?>
                    <p>Sem foto</p>
                <?php endif; ?>
                <input type="hidden" name="current_foto" value="<?= htmlspecialchars($user['foto']) ?>">
            </div>
            <div class="form-group">
                <label for="foto">Atualizar Foto:</label>
                <input type="file" name="foto" id="foto">
                <small>Formato .jpg, até 200 KB, máximo 700x1080px</small>
            </div>
            <button type="submit">Atualizar</button>
        </form>
    </div>
</body>
</html>
