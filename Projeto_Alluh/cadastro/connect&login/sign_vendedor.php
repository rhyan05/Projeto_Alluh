<?php
$success = 0;
$user = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php';
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];

    // Remove pontos e traços do CPF
    $cpf = str_replace(['.', '-'], '', $cpf);

    // Validações em PHP
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "O email deve ser válido.";
    }

    if (!ctype_alpha($username)) {
        $errors[] = "O nome deve conter apenas letras.";
    }

    if (strlen($cpf) !== 11 || !ctype_digit($cpf)) {
        $errors[] = "O CPF deve conter 11 dígitos numéricos.";
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM registration WHERE email='$email'";
        $result = mysqli_query($con, $sql);

        if ($result) {
            $num = mysqli_num_rows($result);
            if ($num > 0) {
                $user = 1;
            } else {
                $sql = "INSERT INTO registration (username, password, email, category, cpf) VALUES ('$username', '$password', '$email', 'seller', '$cpf')";
                $result = mysqli_query($con, $sql);
                if ($result) {
                    $success = 1;
                    header('location: login.php');
                } else {
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>SignUp</title>
</head>
<body>
<?php
if ($user == 1) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error</strong> User already exists.
    </div>';
}

?>
<?php
if ($success == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success</strong> User created.
    </div>';
}

?>


<h1 class="Sign_up">Sign up</h1>
<div class="container">
    <form action="sign_vendedor.php" method="post">
        <div class="form-group">
            <label for="exampleInputname1">Name</label>
            <input type="text" class="form-control" placeholder="Enter your name" name="username" id="username" required>
            <div class="invalid-feedback" id="username-error"></div>
        </div>
        <div class="form-group">
            <label for="exampleInputemail1">Email</label>
            <input type="email" class="form-control" placeholder="Enter your email" name="email" id="email" required>
            <div class="invalid-feedback" id="email-error"></div>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" placeholder="Enter your password" name="password" required>
        </div>
        <div class="form-group">
            <label for="exampleInputcpf1">CPF</label>
            <input type="text" class="form-control" placeholder="000.000.000-00" name="cpf" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" required>
            <div class="invalid-feedback" id="cpf-error"></div>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar Vendedor</button>
        <a class="btn btn-primary" href="sign.php">Cadastro Usuario</a>
        <a class="btn btn-primary" href="login.php">Login</a>
    </form>
</div>

<!-- JavaScript para validações em tempo real -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.querySelector('input[name="email"]');
        const cpfInput = document.querySelector('input[name="cpf"]');
        const usernameInput = document.querySelector('input[name="username"]');
        const emailError = document.querySelector('#email-error');
        const cpfError = document.querySelector('#cpf-error');
        const usernameError = document.querySelector('#username-error');

        emailInput.addEventListener('input', function () {
            if (!validateEmail(emailInput.value)) {
                emailError.textContent = 'O email deve ser válido.';
                emailInput.classList.add('is-invalid');
            } else {
                emailError.textContent = '';
                emailInput.classList.remove('is-invalid');
            }
        });

        cpfInput.addEventListener('input', function () {
            const cpfValue = cpfInput.value.replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cpfValue.length !== 11 || !/^\d{11}$/.test(cpfValue)) {
                cpfError.textContent = 'O CPF deve conter 11 dígitos numéricos.';
                cpfInput.classList.add('is-invalid');
            } else {
                cpfError.textContent = '';
                cpfInput.classList.remove('is-invalid');
                cpfInput.value = formatCPF(cpfValue);
            }
        });

        usernameInput.addEventListener('input', function () {
            if (!validateUsername(usernameInput.value)) {
                usernameError.textContent = 'O nome deve conter apenas letras.';
                usernameInput.classList.add('is-invalid');
            } else {
                usernameError.textContent = '';
                usernameInput.classList.remove('is-invalid');
            }
        });

        function validateEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        function validateUsername(username) {
            const regex = /^[a-zA-Z]+$/;
            return regex.test(username);
        }

        function formatCPF(cpf) {
            return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
        }
    });
</script>

<!-- Optional JavaScript -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>

<style>
    <?php include "sign.css" ?>
</style>
