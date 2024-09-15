<?php
include '../connect&login/connect.php';

session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../connect&login/login.php');
    exit;
}

// Verificar se o userId está definido
if (!isset($_SESSION['userId'])) {
    // Defina isso conforme sua lógica
    $_SESSION['userId'] = $_SESSION['userId'];
}

// Obter o userId da sessão
$userId = $_SESSION['userId'];

// Consulta para pegar as casas cadastradas apenas para o usuário atual
$sql = "SELECT * FROM rent WHERE usuario_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Consulta para pegar as imagens associadas a cada casa
$sqlImagens = "SELECT casa_id, nome_imagem FROM imagens";
$resultImagens = $con->query($sqlImagens);

// Crie um array associativo para armazenar as imagens por casa
$imagensPorCasa = array();
while ($rowImagens = $resultImagens->fetch_assoc()) {
    $casaId = $rowImagens['casa_id'];
    $imagem = $rowImagens['nome_imagem'];
    // Certifique-se de que o array armazena a última imagem por casa_id
    $imagensPorCasa[$casaId] = $imagem;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casas Cadastradas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
</head>
<body>
    <h1 class="Welcome">Bem-vindo, vendedor <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

    <div class="container">
        <a href="../connect&login/logout.php" class="btn btn-primary mt-5">Logout</a>
        <a href="cadastro_prod_vendedor.php" class="btn btn-primary mt-5">Cadastrar Casa</a>
        <br>

        <!-- Formulário de Pesquisa -->
        <form method="POST">
            <label>Pesquisar: </label>
            <input type="text" name="research" class="form-control" value="<?php echo isset($_POST['research']) ? htmlspecialchars($_POST['research']) : ''; ?>"><br><br>
            <input type="submit" value="Pesquisar" name="SendPesqUser" class="btn btn-primary">
        </form>

        <?php
        if (filter_input(INPUT_POST, 'SendPesqUser', FILTER_SANITIZE_SPECIAL_CHARS)) {
            $nome_casa = filter_input(INPUT_POST, 'research', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!empty($nome_casa)) {
                $result_parameters = "SELECT * FROM rent WHERE nome_casa LIKE ? AND usuario_id = ?";
                $stmt_search = $con->prepare($result_parameters);
                $like_nome_casa = "%$nome_casa%";
                $stmt_search->bind_param("si", $like_nome_casa, $userId);
                $stmt_search->execute();
                $result_research = $stmt_search->get_result();
                if ($result_research->num_rows > 0) {
                    echo "<table class='table'>";
                    echo "<thead><tr><th>Nome</th><th>Local</th><th>Preço</th><th>Preview de Imagem</th><th>Ações</th></tr></thead>";
                    echo "<tbody>";
                    while ($row_usuario = $result_research->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row_usuario['nome_casa']) . "</td>";
                        echo "<td>" . htmlspecialchars($row_usuario['local_casa']) . "</td>";
                        echo "<td>" . htmlspecialchars($row_usuario['preco']) . "</td>";
                        echo "<td>";

                        $casaId = $row_usuario['id'];
                        if (isset($imagensPorCasa[$casaId])) {
                            $imagem = $imagensPorCasa[$casaId];
                            $caminhoDaImagem = "../admin/imagens/" . $userId . "/" . $imagem;
                            
                            // Verificar se o arquivo realmente existe
                            if (file_exists($caminhoDaImagem)) {
                                echo "<img src='$caminhoDaImagem?t=" . time() . "' width='100' height='100' alt='Imagem'>";
                            } else {
                                echo "Imagem não encontrada";
                            }
                        } else {
                            echo "Não há imagem";
                        }

                        echo "</td>";
                        echo "<td>";
                        echo "<a class='btn btn-info' href='update_prod_vendedor.php?id=" . $row_usuario['id'] . "'>Edit</a>&nbsp;";
                        echo "<a class='btn btn-danger' href='delete.php?id=" . $row_usuario['id'] . "'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>Nenhum resultado encontrado.</p>";
                }
            }
        }
        ?>

        <h2>Casas Cadastradas</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Local</th>
                    <th>Preço</th>
                    <th>Preview de Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nome_casa']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['local_casa']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['preco']) . "</td>";
                        echo "<td>";

                        $casaId = $row['id'];
                        if (isset($imagensPorCasa[$casaId])) {
                            $imagem = $imagensPorCasa[$casaId];
                            $caminhoDaImagem = "../admin/imagens/" . $userId . "/" . $imagem;

                            // Verificar se o arquivo realmente existe
                            if (file_exists($caminhoDaImagem)) {
                                echo "<img src='$caminhoDaImagem?t=" . time() . "' width='100' height='100' alt='Imagem'>";
                            } else {
                                echo "Imagem não encontrada";
                            }
                        } else {
                            echo "Não há imagem";
                        }

                        echo "</td>";
                        echo "<td>";
                        echo "<a class='btn btn-info' href='update_prod_vendedor.php?id=" . $row['id'] . "'>Edit</a>&nbsp;";
                        echo "<a class='btn btn-danger' href='delete.php?id=" . $row['id'] . "'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nenhuma casa cadastrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
