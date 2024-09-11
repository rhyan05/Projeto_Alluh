<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location: ../connect&login/login.php');
    exit;
}

include '../connect&login/connect.php';

// Atualizar Casa
if (isset($_POST['update'])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $region = $_POST["region"];
    $preco = $_POST["preco"];

    if (!empty($name) && !empty($region) && !empty($preco)) {
        $sql = "UPDATE rent SET nome_casa = ?, local_casa = ?, preco = ? WHERE id = ?";
        $stmt = $con->prepare($sql);

        if ($stmt === false) {
            die('Erro na preparação da consulta: ' . $con->error);
        }

        $stmt->bind_param("ssdi", $name, $region, $preco, $id);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo '<strong>Sucesso!</strong> A Casa foi atualizada com êxito.';
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button></div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo '<strong>Erro!</strong> A Casa não foi atualizada.';
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button></div>';
        }
        $stmt->close();
    } else {
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
        echo '<strong>Atenção!</strong> Preencha todos os campos obrigatórios.';
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span>';
        echo '</button></div>';
    }
}

// Carregar dados da Casa
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM rent WHERE id = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        die('Erro na preparação da consulta: ' . $con->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nome_casa = $row['nome_casa'];
        $local_casa = $row['local_casa'];
        $preco = $row['preco'];
        $user_id = $row['usuario_id']; // Capture user_id for image uploads
    } else {
        header('location: home_vendedor.php');
        exit;
    }
}

// Processar envio de imagens
if (isset($_POST['upload'])) {
    $user_id = $_POST['user_id']; // Ensure this is the user_id
    $house_id = $_POST['house_id']; // Make sure house_id is correctly passed

    // Processar novas imagens
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../admin/imagens/' . $user_id . '/';
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imageName = $_FILES['image']['name'];

            $insertImageSql = "INSERT INTO imagens (casa_id, usuario_id, nome_imagem) VALUES (?, ?, ?)";
            $stmt = $con->prepare($insertImageSql);
            $stmt->bind_param("iis", $house_id, $user_id, $imageName); // Bind user_id here

            if ($stmt->execute()) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo '<strong>Sucesso!</strong> A imagem foi armazenada no banco de dados.';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button></div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '<strong>Erro!</strong> Falha ao armazenar a imagem no banco de dados.';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button></div>';
            }
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo '<strong>Erro!</strong> Falha ao mover a imagem.';
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button></div>';
        }
    }
}

// Processar exclusão de imagens
if (isset($_POST['delete_image'])) {
    $image_id = $_POST['image_id'];

    // Obter o nome do arquivo de imagem
    $sql = "SELECT nome_imagem, usuario_id FROM imagens WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imageName = $row['nome_imagem'];
        $user_id = $row['usuario_id'];
        
        // Remover a imagem do diretório
        $imagePath = '../admin/imagens/' . $user_id . '/' . $imageName;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Excluir a imagem do banco de dados
        $deleteSql = "DELETE FROM imagens WHERE id = ?";
        $stmt = $con->prepare($deleteSql);
        $stmt->bind_param("i", $image_id);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo '<strong>Sucesso!</strong> A imagem foi excluída.';
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button></div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo '<strong>Erro!</strong> Falha ao excluir a imagem no banco de dados.';
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button></div>';
        }
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo '<strong>Erro!</strong> Imagem não encontrada.';
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span>';
        echo '</button></div>';
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" type="image/x-icon" href="../img/engrenagem.ico">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Atualizar Casa</title>

    <style>
        .delete-icon {
            margin-left: 5px;
            background-color: blue;
            transition: background-color 0.3s;
        }

        .delete-icon.clicked {
            background-color: red;
            color: white;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <h1 class="Welcome">Atualizar Casa: <?php echo htmlspecialchars($nome_casa); ?></h1>
    <div class="container">
        <h2>Formulário de Atualização da Casa</h2>
        <form action="" method="post">
            <fieldset>
                <legend>Informação da Casa:</legend>
                <div class="form-group">
                    <label for="name">Nome da Casa</label>
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($nome_casa); ?>" required>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                </div>
                <div class="form-group">
                    <label for="region">Localização</label>
                    <input type="text" class="form-control" name="region" value="<?php echo htmlspecialchars($local_casa); ?>" required>
                </div>
                <div class="form-group">
                    <label for="preco">Preço</label>
                    <input type="number" class="form-control" name="preco" value="<?php echo htmlspecialchars($preco); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" name="update">Atualizar</button>
                <a href="home_vendedor.php" class="btn btn-primary">Home</a>
            </fieldset>
        </form>

        <h2>Gerenciar Imagens</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <input type="hidden" name="house_id" value="<?php echo htmlspecialchars($id); ?>">
            <div class="form-group">
                <label for="image">Enviar Imagem</label>
                <input type="file" class="form-control-file" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary" name="upload">Enviar Imagem</button>
        </form>

        <h2>Imagens da Casa</h2>
        <div class="row">
            <?php
            $sql = "SELECT * FROM imagens WHERE casa_id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $imageId = $row['id'];
                $imageName = $row['nome_imagem'];
                $imagePath = '../admin/imagens/' . $user_id . '/' . $imageName;
                echo '<div class="col-md-4">';
                echo '<div class="card mb-4">';
                echo '<img src="' . htmlspecialchars($imagePath) . '" class="card-img-top" alt="Imagem">';
                echo '<div class="card-body">';
                echo '<form action="" method="post" style="display:inline-block;">';
                echo '<input type="hidden" name="image_id" value="' . htmlspecialchars($imageId) . '">';
                echo '<button type="submit" class="btn btn-danger delete-icon" name="delete_image" onclick="return confirm(\'Tem certeza que deseja excluir esta imagem?\')">Excluir</button>';
                echo '</form>';
                echo '</div></div></div>';
            }
            ?>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5/5N0D5znfD8e78qlzGq2nPYt1tu9zN/ZF/q/5E5H3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.11.0/dist/umd/popper.min.js" integrity="sha384-smh6eA7R2b7aA72pT8beF32B0XqTq3G2b6E1zDAk5t7/sH8ACj1d8yV4dD74/xGZz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8qN6tsb5l7zG73Yy4e8Mzy3Z5B90S1S4B1ON5+06mGVM2ld7c8t" crossorigin="anonymous"></script>

    <script>
        // Script para alterar o estilo do botão de exclusão ao ser clicado
        document.querySelectorAll('.delete-icon').forEach(button => {
            button.addEventListener('click', function() {
                this.classList.add('clicked');
            });
        });
    </script>
</body>
</html>
