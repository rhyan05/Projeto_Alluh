<?php
include '../../connect&login/connect.php';

session_start();
if (!isset($_SESSION['email'])) {
    header('location: ../../connect&login/login.php');
    exit;
}

// Inicialize a variável de mensagem
$message = "";

// Obtenha o usuario_id da URL
$usuario_id = $_GET['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receba os dados do formulário
    $nomeCasa = $con->real_escape_string($_POST['nome_casa']);
    $localCasa = $con->real_escape_string($_POST['local_casa']);
    $preco = $con->real_escape_string($_POST['preco']);

    // Valide e insira os dados na tabela 'rent' com o ID do usuário
    $sql = "INSERT INTO rent (nome_casa, local_casa, preco, usuario_id) VALUES ('$nomeCasa', '$localCasa', '$preco', '$usuario_id')";
    $result = $con->query($sql);

    if ($result) {
        // Dados inseridos com sucesso
        $message = "Aluguel adicionado com sucesso!";
    } else {
        // Trate o erro do banco de dados
        $message = "Erro ao adicionar aluguel: " . $con->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Aluguel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
</head>
<body>
    <h1>Adicionar Aluguel</h1>
    <div class="container">
        <a href="crud_rent.php" class="btn btn-primary mt-5">Voltar para a lista de Aluguéis</a>
        <br><br>
        <?php
        // Exiba a mensagem
        if (!empty($message)) {
            echo "<div class='alert alert-info'>$message</div>";
        }
        ?>
        <form method="POST">
            <!-- Adicione um campo oculto para armazenar o usuario_id -->
            <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
            <div class="form-group">
                <label for="nome_casa">Nome da Casa:</label>
                <input type="text" name="nome_casa" class="form-control" required>
            </div>
            <div class="form-group">
                <label for "local_casa">Local da Casa:</label>
                <input type="text" name="local_casa" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço:</label>
                <input type="number" name="preco" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Adicionar Aluguel</button>
        </form>
    </div>
</body>
</html>
