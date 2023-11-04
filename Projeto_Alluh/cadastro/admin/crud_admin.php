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
<h1 class="Welcome">Bem-vindo admin 
    <?php echo $_SESSION['username'];  ?>
</h1> 

<div class="container">
    <a href="../connect&login/logout.php" class="btn btn-primary mt-5">Logout</a>
    <a href="signup_admin.php" class ='btn btn-primary mt-5'>SignUp</a>
    <a href="crud_admin.php" class="btn btn-primary mt-5">Crud</a>
    <br>

    <form method="POST">
        <label>Pesquisar: </label>
        <input type="text" name="research"><br><br>
        <input type="submit" value="Pesquisar" name="SendPesqUser">
    </form>

    <?php
    $SendPesqUser = filter_input(INPUT_POST, 'SendPesqUser', FILTER_SANITIZE_STRING);

    if ($SendPesqUser) {
        $nome = filter_input(INPUT_POST, 'research', FILTER_SANITIZE_STRING);
        $result_parameters = "SELECT * FROM registration WHERE nome LIKE '%$nome%'";
        $result_research = mysqli_query($con, $result_parameters);
        while ($row_usuario = mysqli_fetch_assoc($result_research)) {
            echo "<tr>";
            echo "<td>" . $row_usuario['id'] . "</td>";
            echo "<td>" . $row_usuario['username'] . "</td>";
            echo "<td>" . $row_usuario['email'] . "</td>";
            echo "<td>" . $row_usuario['category'] . "</td>";
            echo "<td>";
        }
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<div class="container">
    <h2>Usuarios</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Gmail</th>
                <th>category</th>
                <th>Preview de Imagem</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['category'] . "</td>";
                    echo "<td>";

                    // Verifique se o usuário tem imagens
                    if (isset($imagensPorUsuario[$row['id']])) {
                        $imagem = $imagensPorUsuario[$row['id']];
                        $caminhoDaImagem = "imagens/" . $row['id'] . "/" . $imagem;
                        echo "<img src='$caminhoDaImagem' width='100' height='100' alt='Imagem'>";
                    } else {
                        // Caso não tenha imagem
                        echo "Não há imagem";
                    }

                    echo "</td>";

                    echo "<td>";
                    // Verifique a categoria do usuário e exiba o botão "Visualizar"
                    if ($row['category'] == 'seller') {
                        echo "<a class='btn btn-success' href='rent/crud_rent.php?usuario_id=" . $row['id'] . "'>Visualizar</a>&nbsp;";
                    }
                    echo "<a class='btn btn-info' href='update.php?id=" . $row['id'] . "'>Edit</a>&nbsp;";
                    echo "<a class='btn btn-danger' href='delete.php?id=" . $row['id'] . "'>Delete</a>";
                    echo "</td>";

                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
