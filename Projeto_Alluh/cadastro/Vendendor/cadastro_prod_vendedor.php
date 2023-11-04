<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../connect&login/login.php');
    exit(); // Exit to prevent further execution
}

$success = 0;
$user = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "../connect&login/connect.php";
    $nome_casa = $_POST['nome_casa'];
    $local_casa = $_POST['local_casa'];
    $preco = $_POST['preco'];

    // Verifica se há algum campo vazio
    if (empty($nome_casa) || empty($local_casa) || empty($preco)) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error</strong> Please fill in all fields.
        </div>';
    } else {
        // Verifica se o nome da casa já existe no banco de dados
        $check_query = "SELECT * FROM rent WHERE nome_casa = '$nome_casa'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error</strong> A casa ja existe.
            </div>';
        } else {
            // Start a database transaction
            mysqli_begin_transaction($con);

            // Register home
            $sql = "INSERT INTO rent (nome_casa, local_casa, preco) VALUES ('$nome_casa', '$local_casa', '$preco')";
            $result = mysqli_query($con, $sql);

            if ($result) {
                $usuario_id = mysqli_insert_id($con);

                // Create a directory for images
                $diretorio = "../admin/imagens/$usuario_id/";
                if (!is_dir($diretorio)) {
                    mkdir($diretorio, 0755);
                }

                $arquivos = $_FILES['imagens'];

                for ($count = 0; $count < count($arquivos['name']); $count++) {
                    $nome_arquivo = $arquivos['name'][$count];
                    $destino = $diretorio . $arquivos['name'][$count];

                    if (move_uploaded_file($arquivos['tmp_name'][$count], $destino)) {
                        // Insert data into the 'imagens' table
                        $query_imagens = "INSERT INTO imagens (nome_imagem, usuario_id) VALUES (?, ?)";
                        $cad_imagens = mysqli_prepare($con, $query_imagens);

                        mysqli_stmt_bind_param($cad_imagens, 'si', $nome_arquivo, $usuario_id);

                        if (mysqli_stmt_execute($cad_imagens)) {
                            $success = 1;
                        } else {
                            $user = 1;
                        }

                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success</strong> Img Created.
                        </div>';
                    } else {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Fail</strong> Img not created.
                        </div>';
                    }
                }

                // Check if registration was successful
                if ($success == 1) {
                    // Commit the transaction
                    mysqli_commit($con);
                    mysqli_autocommit($con, true); // Restore autocommit

                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success</strong> User Created.
                    </div>';
                    header("refresh:2;url=home_admin.php");
                } else {
                    // Rollback the transaction in case of an error
                    mysqli_rollback($con);
                    mysqli_autocommit($con, true); // Restore autocommit
                    die(mysqli_error($con));
                }
            } else {
                // Rollback the transaction in case of an error
                mysqli_rollback($con);
                mysqli_autocommit($con, true); // Restore autocommit
                die(mysqli_error($con));
            }
        }
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Home</title>
    <style>
        /* Add your custom CSS styles here */
    </style>
</head>
<body>
<h1 class="Welcome">Cadastrar nova casa
    <?php echo $_SESSION['username']; ?>
</h1>

<?php
if ($user == 1) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error</strong> User already exists.
    </div>';
}

if ($success == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success</strong> User Created.
    </div>';
    header("refresh:2;url=home_admin.php");
}
?>

<h1 class="Sign_up">Cadastrar</h1>
<div class="container">
    <form action="home_vendedor.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="exampleInputname1">Nome da Casa</label>
            <input type="text" class="form-control" placeholder="Enter the house name" name="nome_casa">
        </div>
        <div class="form-group">
            <label for="exampleInputname1">Local da Casa</label>
            <input type="text" class="form-control" placeholder="Enter the house location" name="local_casa">
        </div>
        <div class="form-group">
            <label for="exampleInputname1">Preço da Casa</label>
            <input type="text" class="form-control" placeholder="Enter the house price" name="preco">
        </div>
        <div class="form-group">
            <label for="exampleImg">Imagem</label><br>
            <input type="file" name="imagens[]" multiple="multiple"><br>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
        <a class="btn btn-primary" href="home_vendedor.php">Home</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7f"></script>
<style>
  <?php include "../connect&login/sign.css" ?>
</style>
</body>
</html>
