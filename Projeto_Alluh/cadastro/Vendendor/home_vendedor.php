<?php
include '../connect&login/connect.php';

session_start();
if (!isset($_SESSION['email'])) {
    header('location: ../connect&login/login.php');
    exit;
}

// Obtenha o ID do usuário atualmente logado
$userEmail = $_SESSION['email'];
$sqlUserId = "SELECT id FROM registration WHERE email = '$userEmail'";
$resultUserId = $con->query($sqlUserId);
$rowUserId = $resultUserId->fetch_assoc();
$userId = $rowUserId['id'];

// Consulta para pegar os registros do usuário atualmente logado na tabela 'rent'
$sql = "SELECT * FROM rent WHERE usuario_id = $userId";
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
    <style>
        /* Estilos CSS personalizados para a tabela podem ser adicionados aqui */
    </style>
</head>
<body>
<h1 class="Welcome">Bem vindo Vendedor 
    <?php echo $_SESSION['username'];  ?>
</h1> 

<div class "container">
  <a href="../connect&login/logout.php" class="btn btn-primary mt-5">Logout</a>
  <a href="cadastro_prod_vendedor.php" class="btn btn-primary mt-5">Cadastro de Aluguel</a>
  <a href="home_vendedor.php.php" class="btn btn-primary mt-5">Crud</a>
  <br>

<form method="POST">
  <!-- barra de pesquisa -->
    <label>Pesquisar: </label>
    <input type="text" name="research"><br><br>
    <select name="searchBy">
        <option value="local_casa">Região</option>
        <option value="preco">Preço</option>
    </select>
    <input type="submit" value="Pesquisar" name="SendPesqUser">
</form>

<?php
$SendPesqUser = filter_input(INPUT_POST, 'SendPesqUser', FILTER_SANITIZE_STRING);

if (!$SendPesqUser || empty($_POST['research'])) {
    // Se o usuário acessar a página sem pesquisar, ou se a pesquisa estiver vazia, exibir todos os registros de aluguel
    $sql = "SELECT * FROM rent WHERE usuario_id = $userId";
} else {
    // Caso contrário, construir a consulta de acordo com o campo de pesquisa selecionado
    $searchBy = $_POST['searchBy'];
    $research = filter_input(INPUT_POST, 'research', FILTER_SANITIZE_STRING);
    $sql = "SELECT * FROM rent WHERE usuario_id = $userId AND $searchBy LIKE '%$research%'";
}

$result = $con->query($sql);

if ($result === false) {
    echo "<p>Erro na consulta: " . $con->error . "</p>";
} else {
    if ($result->num_rows > 0) {
        // tabela
        echo "<div class='container'>"; 
        echo "<h2>Aluguéis</h2>";
        echo "<table class='table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Nome da Casa</th>";
        echo "<th>Região</th>";
        echo "<th>Gmail</th>";
        echo "<th>Preço</th>";
        echo "<th>Preview de Imagem</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['nome_casa'] . "</td>";
            echo "<td>" . $row['local_casa'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['preco'] . "</td>";
            echo "<td>";

            if (isset($imagensPorUsuario[$row['usuario_id']])) {
                $imagem = $imagensPorUsuario[$row['usuario_id']];
                $caminhoDaImagem = "imagens/" . $row['usuario_id'] . "/" . $imagem;
                echo "<img src='$caminhoDaImagem' width='100' height='100' alt='Imagem'>";
            } else {
                echo "Não há imagem";
            }

            echo "</td>";
            echo "<td><a class='btn btn-info' href='update.php?id=" . $row['id'] . "'>Edit</a>&nbsp;";
            echo "<a class='btn btn-danger' href='delete.php?id=" . $row['id'] . "&usuario_id=" . $row['usuario_id'] . "'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>Você ainda não possui aluguéis.</p>";
    }
}
?>

</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
