<?php
$success = false;
$userExists = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php';

    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Verificar se os campos estão vazios
    if (empty($username)) {
        $errors[] = "O campo Nome é obrigatório.";
    }

    if (empty($password)) {
        $errors[] = "O campo Senha é obrigatório.";
    }

    if (empty($email)) {
        $errors[] = "O campo Email é obrigatório.";
    }

    // Verificar se o nome contém apenas letras
    if (!preg_match("/^[a-zA-Z]+$/", $username)) {
        $errors[] = "O campo Nome deve conter apenas letras.";
    }

    // Verificar se o endereço de e-mail é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "O endereço de email é inválido.";
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM registration WHERE email='$email'";
        $result = mysqli_query($con, $sql);

        if ($result) {
            $num = mysqli_num_rows($result);
            if ($num > 0) {
                $userExists = true;
            } else {
                $sql = "INSERT INTO registration (username, password, email, category) VALUES ('$username', '$password', '$email', 'comum')";
                $result = mysqli_query($con, $sql);
                if ($result) {
                    $success = true;
                    header('location: login.php');
                } else {
                    die(mysqli_error($con));
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>SignUp Usuario</title>
</head>
<body>
<?php
if ($userExists) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error</strong> Usuário já existe.
          </div>';
}

if ($success) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sucesso</strong> Usuário criado.
        </div>';
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . $error . '
              </div>';
    }
}
?>
<h1 class="Sign_up">Sign up Usuário</h1>
<div class="container">
    <form action="sign.php" method="post">
        <div class="form-group">
            <label for="exampleInputname1">Name</label>
            <input type="text" class="form-control" placeholder="Enter your name" name="username">
        </div>
        <div class="form-group">
            <label for="exampleInputemail1">Email</label>
            <input type="email" class="form-control"  placeholder="Enter your email" name="email">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" id="password" class="form-control"  placeholder="Enter your password" name="password">
            <button type="button" id="password-toggle" class="btn btn-secondary mt-2" onclick="togglePasswordVisibility()">Mostrar senha</button>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar Usuário</button>
        <a class="btn btn-primary" href="sign_vendedor.php">Cadastro Vendedor</a>
        <a class="btn btn-primary" href="login.php">Login</a>
    </form>
</div>

<script>
    function togglePasswordVisibility() {
        var passwordField = document.getElementById('password');
        var passwordToggle = document.getElementById('password-toggle');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            passwordToggle.textContent = 'Ocultar senha';
        } else {
            passwordField.type = 'password';
            passwordToggle.textContent = 'Mostrar senha';
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

<style>
        <?php include "sign.css" ?>
</style>
