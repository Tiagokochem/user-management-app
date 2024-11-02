<?php
$mysqli = new mysqli("mysql", "user", "password", "user_management");

$errors = []; // Array para armazenar mensagens de erro

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $data_nascimento = $_POST['data_nascimento'];

    // Validação do Nome
    if (strlen($nome) < 3) {
        $errors[] = "Nome precisa ter ao menos 3 caracteres.";
    }

    // Validação do Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido.";
    } else {
        // Verificar se o email é único
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
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
        $fotoName = $_FILES['foto']['name']; // Obter o nome original do arquivo
        $fotoExtension = strtolower(pathinfo($fotoName, PATHINFO_EXTENSION)); // Definir a extensão do arquivo
        $fotoInfo = getimagesize($fotoTmpPath);
    
        // Verificar o tamanho do arquivo
        if ($fotoSize > 200000) {
            $errors[] = "A foto deve ter no máximo 200 KB.";
        }
    
        // Verificar as dimensões da imagem
        if ($fotoInfo[0] > 700 || $fotoInfo[1] > 1080) {
            $errors[] = "A foto deve ter no máximo 700x1080 pixels.";
        }
    
        // Verificar o formato do arquivo
        if (!in_array($fotoExtension, ['jpg', 'jpeg'])) {
            $errors[] = "A foto deve estar em formato .jpg ou .jpeg.";
        }
    
        // Salvar a foto se não houver erros
        if (empty($errors)) {
            $fotoName = uniqid() . ".jpg";
            $fotoUploadPath = "uploads/" . $fotoName;
            move_uploaded_file($fotoTmpPath, $fotoUploadPath);
        }
    } else {
        $errors[] = "Erro no upload da foto. Verifique o arquivo.";
    }
    

    // Inserir os dados no banco se não houver erros
    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO users (nome, email, data_nascimento, foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $data_nascimento, $fotoName);
        $stmt->execute();
        header("Location: index.php?message=Usuário+criado+com+sucesso");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Adicionar Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
        }

        .menu {
            margin-bottom: 20px;
        }

        .menu a {
            margin-right: 10px;
            text-decoration: none;
            color: #007BFF;
        }

        .menu a:hover {
            text-decoration: underline;
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
        }

        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            padding: 10px;
            border: 1px solid #f5c6cb;
            margin-bottom: 20px;
        }

        .success-message {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <!-- Menu simples -->
    <div class="menu">
        <a href="index.php">Início</a>
        <a href="add_user.php">Adicionar Usuário</a>
    </div>

    <h1>Adicionar Novo Usuário</h1>

    <!-- Exibir mensagens de erro se existirem -->
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
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nome ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" name="data_nascimento" id="data_nascimento" value="<?= htmlspecialchars($data_nascimento ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto:</label>
                <input type="file" name="foto" id="foto" required>
                <small>Formato .jpg, até 200 KB, máximo 700x1080px</small>
            </div>
            <button type="submit">Salvar</button>
        </form>
    </div>
</body>

</html>