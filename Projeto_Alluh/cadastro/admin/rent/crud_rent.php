<?php
include '../../connect&login/connect.php';

session_start();
if (!isset($_SESSION['email'])) {
    header('location: ../../connect&login/login.php');
    exit;
}

// Verifique se o usuario_id foi passado na URL ou se já está na sessão
if (isset($_GET['usuario_id'])) {
    $usuario_id = $_GET['usuario_id'];
    $_SESSION['usuario_id'] = $usuario_id; // Armazena o usuario_id na sessão
} else if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id']; // Usa o usuario_id da sessão
} else {
    // Se 'usuario_id' não estiver na sessão ou na URL, redirecione para a página onde o usuário deve selecionar um 'usuario_id'.
    header('location: selecione_usuario.php');
    exit;
}

// Consulta SQL para pegar os aluguéis do usuário especificado
$sql = "SELECT * FROM rent WHERE usuario_id = $usuario_id";
$result = $con->query($sql);

if (!$result) {
    echo "Erro na consulta SQL: " . $con->error;
    exit; // Encerre o script em caso de erro.
}

// Consulta SQL para obter o nome do usuário com base no usuario_id
$sql_user = "SELECT username FROM registration WHERE ID = $usuario_id";
$result_user = $con->query($sql_user);

if ($result_user && $row_user = $result_user->fetch_assoc()) {
    $username = $row_user['username'];
} else {
    $username = "Usuário Desconhecido";
}

// Pesquisa de aluguéis
if (isset($_POST['SendPesqUser'])) {
    $nome = filter_input(INPUT_POST, 'research', FILTER_SANITIZE_STRING);
    $result_parameters = "SELECT * FROM rent WHERE usuario_id = $usuario_id AND nome_casa LIKE '%$nome%'";
    $result_research = mysqli_query($con, $result_parameters);
} else {
    $result_research = $result; // Exibe todos os aluguéis se nenhuma pesquisa for realizada.
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Aluguéis</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/le592b5726.js" crossorigin="anonymous"></script>
</head>
<body>
    <h1 class="Welcome">Bem-vindo, <?php echo $_SESSION['username']; ?></h1>
    <h2>Você está visualizando os alugeis do usuario: <?php echo $username; ?></h2>

    <div class="container">
        <a href="../../connect&login/logout.php" class="btn btn-primary mt-5">Logout</a>
        <a href="signup_admin.php" class="btn btn-primary mt-5">SignUp</a>
        <a href="../crud_admin.php" class="btn btn-primary mt-5">Crud</a>
        <form method="POST">
            <div class="form-group">
                <label for="research">Pesquisar:</label>
                <input type="text" name="research" id="research" class="form-control" placeholder="Digite o que você quer buscar">
            </div>
            <button type="submit" name="SendPesqUser" class="btn btn-primary">Pesquisar</button>
        </form>

        <br>

        <h2>Aluguéis</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nome da Casa</th>
                    <th>Local da Casa</th>
                    <th>Preço</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_research->num_rows > 0) {
                    while ($row = $result_research->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['nome_casa'] . "</td>";
                        echo "<td>" . $row['local_casa'] . "</td>";
                        echo "<td>R$ " . $row['preco'] . "</td>";
                        echo "<td>
                                <a class='btn btn-info' href='edit_rent.php?id=" . $row['id'] . "'>Editar</a>
                                <a class='btn btn-danger' href='delete_rent.php?id=" . $row['id'] . "'>Excluir</a>
                              </td>";
                        echo "</tr>";   
                    }
                } else {
                    echo "Não há aluguéis correspondentes a essa pesquisa.";
                }
                ?>
            </tbody>
        </table>

        <a href="add_rent.php?usuario_id=<?php echo $usuario_id; ?>" class="btn btn-success">Adicionar Aluguel</a>
    </div>
</body>
</html>
