<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../connect&login/login.php');
    exit();
}

$userId = $_SESSION['userId'];
$success = 0;
$image_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "../connect&login/connect.php";
    $nome_casa = $_POST['nome_casa'];
    $local_casa = $_POST['local_casa'];
    $preco = $_POST['preco'];

    if (empty($_FILES['imagens']['name'][0])) {
        $image_error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error</strong> Please select at least one image.
        </div>';
    } else {
        if (empty($nome_casa) || empty($local_casa) || empty($preco)) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error</strong> Please fill in all fields.
            </div>';
        } else {
            $check_query = "SELECT * FROM rent WHERE nome_casa = '$nome_casa'";
            $check_result = mysqli_query($con, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error</strong> A casa já existe.
                </div>';
            } else {
                mysqli_begin_transaction($con);

                // Inserir a nova casa e pegar o ID gerado
                $sql = "INSERT INTO rent (nome_casa, local_casa, preco, usuario_id) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, 'ssdi', $nome_casa, $local_casa, $preco, $userId);

                if (mysqli_stmt_execute($stmt)) {
                    $house_id = mysqli_insert_id($con);
                    $diretorio = "../admin/imagens/$userId/";
                    if (!is_dir($diretorio)) {
                        mkdir($diretorio, 0755);
                    }

                    $arquivos = $_FILES['imagens'];

                    // Preparar a consulta de inserção de imagens
                    $query_imagens = "INSERT INTO imagens (casa_id, usuario_id, nome_imagem) VALUES (?, ?, ?)";
                    $stmt_imagens = mysqli_prepare($con, $query_imagens);
                    
                    $success = 1;
                    for ($count = 0; $count < count($arquivos['name']); $count++) {
                        $nome_arquivo = $arquivos['name'][$count];
                        $destino = $diretorio . $nome_arquivo;

                        if (move_uploaded_file($arquivos['tmp_name'][$count], $destino)) {
                            // Associar a imagem à casa correta
                            mysqli_stmt_bind_param($stmt_imagens, 'iis', $house_id, $userId, $nome_arquivo);

                            if (!mysqli_stmt_execute($stmt_imagens)) {
                                $success = 0;
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error</strong> Image not inserted into the database.
                                </div>';
                                break;
                            }
                        } else {
                            $success = 0;
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error</strong> Image not created.
                            </div>';
                            break;
                        }
                    }

                    if ($success == 1) {
                        mysqli_commit($con);
                        mysqli_autocommit($con, true);
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success</strong> House and images added successfully.
                        </div>';
                        header("refresh:2;url=home_vendedor.php");
                    } else {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, true);
                        die(mysqli_error($con));
                    }
                } else {
                    mysqli_rollback($con);
                    mysqli_autocommit($con, true);
                    die(mysqli_error($con));
                }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Cadastrar Nova Casa</title>
    <style>
        /* Add your custom CSS styles here */
    </style>
</head>
<body>
<h1 class="Welcome">Cadastrar nova casa <?php echo $_SESSION['username']; ?></h1>

<?php
if ($image_error) {
    echo $image_error;
}
?>

<h1 class="Sign_up">Cadastrar</h1>
<div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome_casa">Nome da Casa</label>
            <input type="text" class="form-control" placeholder="Enter the house name" name="nome_casa" required>
        </div>
        <div class="form-group">
            <label for="local_casa">Local da Casa</label>
            <input type="text" class="form-control" placeholder="Enter the house location" name="local_casa" required>
        </div>
        <div class="form-group">
            <label for="preco">Preço da Casa</label>
            <input type="text" class="form-control" placeholder="Enter the house price" name="preco" required>
        </div>
        <h2>Imagens da Casa</h2>
        <input type="file" name="imagens[]" accept="image/*" multiple>
        <br><br>
        <input type="submit" value="Cadastrar Casa" class="btn btn-primary">
        <a class="btn btn-primary forms_botoes" href="home_vendedor.php">Voltar</a>

    </form>
</div>

<script>
function confirmDelete(imageId) {
    var confirmation = confirm("Tem certeza de que deseja excluir esta imagem?");
    if (confirmation) {
        fetch("delete_image.php?image_id=" + imageId)
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert('Exclusão bem-sucedida');
                    window.location.reload();
                } else {
                    alert('Erro ao excluir a imagem');
                }
            })
            .catch(error => {
                alert('Erro ao excluir a imagem');
            });
    }
}
</script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7f"></script>
</body>
</html>
