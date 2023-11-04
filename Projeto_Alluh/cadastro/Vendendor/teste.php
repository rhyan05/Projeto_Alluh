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

// CRUD para a tabela "rent"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'create') {
            // Lógica para criar um novo registro na tabela "rent"
            $inome_casa = $_POST['inome_casa'];
            $local_casa = $_POST['local_casa'];
            $preco = $_POST['preco'];
            $usuario_id = $_POST['usuario_id'];

            // Execute a consulta de inserção aqui
            $insertSql = "INSERT INTO rent (inome_casa, local_casa, preco, usuario_id) VALUES ('$inome_casa', '$local_casa', '$preco', '$usuario_id')";
            if ($con->query($insertSql)) {
                // Registro inserido com sucesso
            } else {
                // Trate o erro de inserção
            }
        } elseif ($action === 'update') {
            // Lógica para atualizar um registro na tabela "rent"
            $rent_id = $_POST['rent_id'];
            $inome_casa = $_POST['inome_casa'];
            $local_casa = $_POST['local_casa'];
            $preco = $_POST['preco'];
            $usuario_id = $_POST['usuario_id'];

            // Execute a consulta de atualização aqui
            $updateSql = "UPDATE rent SET inome_casa = '$inome_casa', local_casa = '$local_casa', preco = '$preco', usuario_id = '$usuario_id' WHERE rent_id = $rent_id";
            if ($con->query($updateSql)) {
                // Registro atualizado com sucesso
            } else {
                // Trate o erro de atualização
            }
        } elseif ($action === 'delete') {
            // Lógica para excluir um registro na tabela "rent"
            $rent_id = $_POST['rent_id'];

            // Execute a consulta de exclusão aqui
            $deleteSql = "DELETE FROM rent WHERE rent_id = $rent_id";
            if ($con->query($deleteSql)) {
                // Registro excluído com sucesso
            } else {
                // Trate o erro de exclusão
            }
        }
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
    <style>
        /* Estilos CSS personalizados para a tabela podem ser adicionados aqui */
    </style>
</head>
<body>
<h1 class="Welcome">Bem vindo admin 
    <?php echo $_SESSION['username'];  ?>
    </h1> 

    <div class="container">
      <a href="../connect&login/logout.php" class="btn btn-primary mt-5">Logout</a>
      <a href="signup_admin.php" class="btn btn-primary mt-5">SignUp</a>
      <a href="crud_admin.php"class="btn btn-primary mt-5">Crud</a>
      <br>

    <form method="POST">
        <label>Pesquisar: </label>
        <input type="text" name="research"><br><br>
        <select name="searchBy">
            <option value="id">ID</option>
            <option value="username">Nome</option>
            <option value="email">Gmail</option>
            <option value="category">Category</option>
        </select>
        <input type="submit" value="Pesquisar" name="SendPesqUser">
    </form>

    <?php
    $SendPesqUser = filter_input(INPUT_POST, 'SendPesqUser', FILTER_SANITIZE_STRING);

    if (!$SendPesqUser || empty($_POST['research'])) {
        // Se o usuário acessar a página sem pesquisar, ou se a pesquisa estiver vazia, exibir todos os usuários
        $sql = "SELECT * FROM registration";
    } else {
        // Caso contrário, construir a consulta de acordo com o campo de pesquisa selecionado
        $searchBy = $_POST['searchBy'];
        $research = filter_input(INPUT_POST, 'research', FILTER_SANITIZE_STRING);
        $sql = "SELECT * FROM registration WHERE $searchBy LIKE '%$research%'";
    }

    $result = $con->query($sql);

    if ($result) {
        // Restante do código para exibir a tabela e os resultados permanece igual
        echo "<div class='container'>";
        echo "<h2>Usuarios</h2>";
        echo "<table class='table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Nome</th>";
        echo "<th>Gmail</th>";
        echo "<th>Category</th>";
        echo "<th>Preview de Imagem</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['category'] . "</td>";
            echo "<td>";

            if (isset($imagensPorUsuario) && isset($imagensPorUsuario[$row['id']])) {
                $imagem = $imagensPorUsuario[$row['id']];
                $caminhoDaImagem = "imagens/" . $row['id'] . "/" . $imagem;
                echo "<img src='$caminhoDaImagem' width='100' height='100' alt='Imagem'>";
            } else {
                echo "Não há imagem";
            }

            echo "</td>";
            echo "<td><a class='btn btn-info' href='update.php?id=" . $row['id'] . "'>Edit</a>&nbsp;";
            echo "<a class='btn btn-danger' href='delete.php?id=" . $row['id'] . "'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>Erro na consulta.</p>";
    }
    ?>

    <!-- Adicione formulários para criar, atualizar e excluir registros na tabela "rent" -->
    <form method="POST">
        <h2>Criar novo registro de aluguel</h2>
        <input type="hidden" name="action" value="create">
        <label>Nome da casa:</label>
        <input type="text" name="inome_casa"><br>
        <label>Local da casa:</label>
        <input type="text" name="local_casa"><br>
        <label>Preço:</label>
        <input type="text" name="preco"><br>
        <label>ID do usuário:</label>
        <input type="text" name="usuario_id"><br>
        <input type="submit" value="Criar Registro">
    </form>

    <!-- Formulário para atualizar registro de aluguel -->
    <form method="POST">
        <h2>Atualizar registro de aluguel</h2>
        <input type="hidden" name="action" value="update">
        <label>ID do registro de aluguel:</label>
        <input type="text" name="rent_id"><br>
        <label>Nome da casa:</label>
        <input type="text" name="inome_casa"><br>
        <label>Local da casa:</label>
        <input type="text" name="local_casa"><br>
        <label>Preço:</label>
        <input type="text" name="preco"><br>
        <label>ID do usuário:</label>
        <input type="text" name="usuario_id"><br>
        <input type="submit" value="Atualizar Registro">
    </form>

    <!-- Formulário para excluir registro de aluguel -->
    <form method="POST">
        <h2>Excluir registro de aluguel</h2>
        <input type="hidden" name="action" value="delete">
        <label>ID do registro de aluguel:</label>
        <input type="text" name="rent_id"><br>
        <input type="submit" value="Excluir Registro">
    </form>

    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
