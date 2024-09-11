<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../connect&login/login.php');
    exit(); // Exit to prevent further execution
}

$success = 0;
$user = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../connect&login/connect.php';
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $category = $_POST['category'];

    // Check if any of the form fields are empty
    if (empty($username) || empty($password) || empty($email) || empty($category)) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error</strong> Please fill in all fields.
        </div>';
    } else {
        $sql = "SELECT * FROM registration WHERE email='$email'";
        $result = mysqli_query($con, $sql); // Use the correct connection variable

        if ($result) {
            $num = mysqli_num_rows($result);
            if ($num > 0) {
                $user = 1;
            } else {
                // Register users
                $sql = "INSERT INTO registration (username, password, email, category) VALUES ('$username', '$password', '$email', '$category')";
                $result = mysqli_query($con, $sql); // Use the correct connection variable
                
                if ($result) {
                    $usuario_id = mysqli_insert_id($con); // Get the last inserted ID
                    
                    $diretorio = "imagens/$usuario_id/";
                    // Create a directory
                    if (!is_dir($diretorio)) {
                        mkdir($diretorio, 0755); // Permissions 0755
                    }

                    $arquivos = $_FILES['imagens'];

                    for ($count = 0; $count < count($arquivos['name']); $count++) {
                        $nome_arquivo = $arquivos['name'][$count];
                        $destino = $diretorio . $arquivos['name'][$count];

                        if (move_uploaded_file($arquivos['tmp_name'][$count], $destino)) {
                            $query_imagens = "INSERT INTO imagens (nome_imagem, usuario_id) VALUES (?, ?)";
                            $cad_imagens = mysqli_prepare($con, $query_imagens); // Use mysqli_prepare

                            mysqli_stmt_bind_param($cad_imagens, 'si', $nome_arquivo, $usuario_id); // Bind parameters

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
                                <strong>Fail</strong> Img dont created.
                            </div>';
                        }
                    }

                    // Check if registration was successful
                    if ($success == 1) {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success</strong> User Created.
                        </div>';
                        header("refresh:2;url=home_admin.php");
                    } else {
                        die(mysqli_error($con));
                    }
                } else {
                    die(mysqli_error($con));
                }
            }
        } else {
            die(mysqli_error($con));
        }
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" type="imagex/png" href="../img/engrenagem.ico">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Home</title>
</head>
<body>
<h1 class="Welcome">Bem vindo admin
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

<h1 class="Sign_up">Sign up</h1>
<div class="container">
    <form action="signup_admin.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="exampleInputname1">Name</label>
            <input type="text" class="form-control" placeholder="Enter your name" name="username">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Email</label>
            <input type="email" class="form-control"  placeholder="Enter your email" name="email">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control"  placeholder="Enter your password" name="password">
        </div>
        <div class="form-group">
            <label for="exampleInputCategory1">Category</label>
            <br>
            <select name="category" id="user_type">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleImg">Imagem</label><br>
            <input type="file" name="imagens[]" multiple="multiple"><br>
        </div>
        <button type="submit" class="btn btn-primary">Sign Up</button>
        <a class="btn btn-primary" href="home_admin.php">Home</a>
    </form>
</div>

<!-- Optional JavaScript -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7f"