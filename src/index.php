<?php
$mysqli = new mysqli("mysql", "user", "password", "user_management");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM users WHERE nome LIKE ? OR email LIKE ? LIMIT 10";
$stmt = $mysqli->prepare($query);
$searchTerm = "%$search%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$successMessage = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .success-message { background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb; display: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        a.button { padding: 8px 12px; color: white; background-color: #007BFF; text-decoration: none; border-radius: 4px; margin-right: 5px; }
        a.button:hover { background-color: #0056b3; }
        img.thumbnail { width: 50px; cursor: pointer; transition: 0.3s; }
        img.thumbnail:hover { opacity: 0.7; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.7); }
        .modal-content { margin: 15% auto; display: block; width: 50%; max-width: 700px; }
    </style>
</head>
<body>

    <h1>Usuários</h1>

    <?php if ($successMessage): ?>
    <div class="success-message" id="success-message">
        <?= htmlspecialchars($successMessage) ?>
    </div>
    <?php endif; ?>

    <a href="add_user.php" class="button">Adicionar Novo Usuário</a>

    <form method="get" style="margin-top: 20px;">
        <input type="text" name="search" placeholder="Buscar por nome ou email" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Buscar</button>
    </form>

    <table>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Data de Nascimento</th>
            <th>Foto</th>
            <th>Ações</th>
        </tr>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['nome']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= date("d/m/Y", strtotime($user['data_nascimento'])) ?></td>
                <td>
                    <?php if ($user['foto']): ?>
                        <img src="uploads/<?= htmlspecialchars($user['foto']) ?>" class="thumbnail" onclick="showModal('uploads/<?= htmlspecialchars($user['foto']) ?>')">
                    <?php else: ?>
                        Sem foto
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="button">Editar</a>
                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="button" style="background-color: #dc3545;" onclick="return confirmDeletion()">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Modal para exibir imagem -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        // Mostrar mensagem de sucesso temporariamente
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.getElementById('success-message');
            if (message) {
                message.style.display = 'block';
                setTimeout(() => { message.style.display = 'none'; }, 3000);
            }
        });

        // Confirmar exclusão
        function confirmDeletion() {
            return confirm('Tem certeza de que deseja excluir este usuário? Esta ação não pode ser desfeita.');
        }

        // Função para exibir a imagem em um modal
        function showModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modal.style.display = 'block';
            modalImage.src = imageSrc;
        }

        // Fechar modal de imagem
        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        }
    </script>

</body>
</html>
