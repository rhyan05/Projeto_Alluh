<?php
include '../../connect&login/connect.php';

session_start();
if (!isset($_SESSION['email'])) {
    header('location: ../../connect&login/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receba os dados do formulário
    $id = $_POST['id'];
    $nomeCasa = $_POST['nome_casa'];
    $localCasa = $_POST['local_casa']; // Correção aqui
    $preco = $_POST['preco'];

    // Valide e atualize os dados na tabela 'rent'
    $sql = "UPDATE rent SET nome_casa = '$nomeCasa', local_casa = '$localCasa', preco = $preco WHERE id = $id";
    $result = $con->query($sql);

    if ($result) {
        // Redirecione para a página CRUD com uma mensagem de sucesso
        header('Location: crud_rent.php?success=true');
        exit;
    } else {
        // Em caso de erro, você pode exibir uma mensagem de erro ou tomar outra ação aqui.
    }
} elseif (isset($_GET['id'])) {
    // Receba o 'id' do aluguel a ser editado
    $id = $_GET['id'];

    // Consulta para obter os detalhes do aluguel
    $sql = "SELECT * FROM rent WHERE id = $id";
    $result = $con->query($sql);
    $row = $result->fetch_assoc();
} else {
    // Redirecione de volta para a página de CRUD de aluguéis se o 'id' não estiver definido
    header('Location: crud_rent.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluguel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
</head>
<body>
    <h1>Editar Aluguel</h1>
    <div class="container">
        <a href="crud_rent.php" class="btn btn-primary mt-5">Voltar para a lista de Aluguéis</a>
        <br><br>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <div class="form-group">
                <label for="nome_casa">Nome da Casa:</label>
                <input type="text" name="nome_casa" class="form-control" value="<?php echo $row['nome_casa']; ?>" required>
            </div>
            <div class="form-group">
                <label for="local_casa">Local da Casa:</label>
                <input type="text" name="local_casa" class="form-control" value="<?php echo $row['local_casa']; ?>" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço:</label>
                <input type="number" name="preco" class="form-control" value="<?php echo $row['preco']; ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>
