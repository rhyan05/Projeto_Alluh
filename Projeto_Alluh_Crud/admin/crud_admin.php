<?php
include '../connect&login/connect.php';

session_start();
if (!isset($_SESSION['email'])) {
    header('location: ../connect&login/login.php');
    exit;
}

// Consulta para pegar os usuários
$sql = "SELECT * FROM registration";
$result = $con->query($sql);

// Consulta para pegar a primeira imagem de cada usuário
$sqlImagens = "SELECT usuario_id, MIN(nome_imagem) AS nome_imagem FROM imagens GROUP BY usuario_id";
$resultImagens = $con->query($sqlImagens);

// Crie um array associativo para armazenar as imagens
$imagensPorUsuario = array();
while ($rowImagens = $resultImagens->fetch_assoc()) {
    $usuarioId = $rowImagens['usuario_id'];
    $imagem = $rowImagens['nome_imagem'];
    $imagensPorUsuario[$usuarioId] = $imagem;
}

$usuarios = $result->fetch_all(MYSQLI_ASSOC); // Armazena todos os usuários
$pesquisa = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['SendPesqUser'])) {
    $nome = filter_input(INPUT_POST, 'research', FILTER_SANITIZE_SPECIAL_CHARS);
    $result_research = $con->query("SELECT * FROM registration WHERE username LIKE '%$nome%'");
    
    if ($result_research->num_rows > 0) {
        $usuarios = $result_research->fetch_all(MYSQLI_ASSOC); // Atualiza a lista com os resultados da pesquisa
        $pesquisa = true;
    } else {
        $usuarios = [];
        $mensagem = "Nenhum resultado encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crud</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/le592b5726.js" crossorigin="anonymous"></script>
</head>
<body>
    <h1 class="Welcome">Bem-vindo admin <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

    <div class="container">
        <a href="../connect&login/logout.php" class="btn btn-primary mt-5">Logout</a>
        <a href="signup_admin.php" class="btn btn-primary mt-5">SignUp</a>
        <a href="crud_admin.php" class="btn btn-primary mt-5">Crud</a>
        <br>

        <form method="POST">
            <label>Pesquisar: </label>
            <input type="text" name="research" class="form-control"><br><br>
            <input type="submit" value="Pesquisar" name="SendPesqUser" class="btn btn-primary">
        </form>

        <?php if (isset($mensagem)): ?>
            <p><?php echo htmlspecialchars($mensagem); ?></p>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2><?php echo $pesquisa ? "Resultados da Pesquisa" : "Usuários"; ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Gmail</th>
                    <th>Categoria</th>
                    <th>Preview de Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody class="tbody">
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>
                                <?php
                                $usuarioId = $row['id'];
                                if (isset($imagensPorUsuario[$usuarioId])) {
                                    $imagem = $imagensPorUsuario[$usuarioId];
                                    $caminhoDaImagem = "imagens/" . $usuarioId . "/" . $imagem;
                                    echo "<img src='$caminhoDaImagem?t=" . time() . "' width='100' height='100' alt='Imagem'>";
                                } else {
                                    echo "Não há imagem";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($row['category'] == 'seller'): ?>
                                    <a class='btn btn-success' href='rent/crud_rent.php?usuario_id=<?php echo $row['id']; ?>'>Visualizar</a>&nbsp;
                                <?php endif; ?>
                                <a class='btn btn-info' href='update.php?id=<?php echo $row['id']; ?>'>Edit</a>&nbsp;
                                <a class='btn btn-danger' href='delete.php?id=<?php echo $row['id']; ?>'>Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">Nenhum usuário cadastrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <style>
        .tbody{
            text-align: center;
            vertical-align: middle; /* Centraliza verticalmente */
        }
        .container {
            margin-top: 20px;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
