<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../connect&login/login.php');
    exit(); // Exit to prevent further execution
}

include '../connect&login/connect.php';

$success = 0;
$user = 0;
$nome_usuario_atualizado = ""; // Variável para armazenar o nome do usuário que está sendo editado
$categoria_usuario = ""; // Variável para armazenar a categoria do usuário
$CPF = ''; // Inicia vazio
$email_usuario = ''; // Variável para armazenar o email do usuário
$password_usuario = ''; // Variável para armazenar a senha do usuário

// Verifica se o ID do usuário foi passado
if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // Busca o nome do usuário e outras informações a serem editadas no banco de dados
    $sql_usuario = "SELECT * FROM registration WHERE id = ?";
    $stmt = mysqli_prepare($con, $sql_usuario);
    mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
    mysqli_stmt_execute($stmt);
    $result_usuario = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result_usuario)) {
        $nome_usuario_atualizado = $row['username'];
        $categoria_usuario = $row['category']; // Armazena a categoria atual do usuário
        $CPF = $row['CPF']; // Armazena o CPF, se houver
        $email_usuario = $row['email']; // Armazena o email
        $password_usuario = $row['password']; // Armazena a senha
    } else {
        echo 'Usuário não encontrado';
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $category = $_POST['category'];
    $CPF = isset($_POST['cpf']) ? $_POST['cpf'] : '';

    // Check if any of the form fields are empty
    if (empty($username) || empty($password) || empty($email) || empty($category) || ($category == 'seller' && empty($CPF))) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error</strong> Please fill in all fields.
        </div>';
    } else {
        $sql = "UPDATE registration SET username = ?, password = ?, email = ?, category = ?, CPF = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt_update, 'sssssi', $username, $password, $email, $category, $CPF, $id_usuario);
        $result_update = mysqli_stmt_execute($stmt_update);

        if ($result_update) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success</strong> User updated successfully.
            </div>';
            header("refresh:2;url=crud_admin.php");
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

    <link rel="shortcut icon" type="image/x-icon" href="../img/engrenagem.ico">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Update</title>

    <style>
    /* Adiciona padding entre o botão de deletar e a imagem */
    .delete-icon {
        margin-left: 10px; /* Adiciona o espaço desejado */
    }
    .forms_botoes{
        margin-bottom: 10px;
    }
    </style>

    <script>
    // Função para formatar o CPF
    function formatCPF(cpf) {
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    }

    // Função para exibir ou ocultar o campo CPF
    function showCpfField() {
        var category = document.getElementById('user_type').value;
        var cpfField = document.getElementById('cpf_field');
        var cpfInput = document.querySelector('input[name="cpf"]');
        
        if (category === 'seller') {
            cpfField.style.display = 'block';
        } else {
            cpfField.style.display = 'none';
            cpfInput.value = ''; // Limpa o valor do campo CPF
        }
    }

    // Função para alternar a visibilidade da senha
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

    document.addEventListener('DOMContentLoaded', function () {
        const cpfInput = document.querySelector('input[name="cpf"]');
        const cpfError = document.querySelector('#cpf-error');

        // Validação do CPF
        cpfInput.addEventListener('input', function () {
            const cpfValue = cpfInput.value.replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cpfValue.length !== 11 || !/^\d{11}$/.test(cpfValue)) {
                cpfError.textContent = 'O CPF deve conter 11 dígitos numéricos.';
                cpfInput.classList.add('is-invalid');
            } else {
                cpfError.textContent = '';
                cpfInput.classList.remove('is-invalid');
                cpfInput.value = formatCPF(cpfValue); // Formata o CPF
            }
        });
    });
    </script>
</head>
<body>

<h1 class="Welcome">Editando usuário: <?php echo htmlspecialchars($nome_usuario_atualizado); ?></h1>

<div class="container">
    <form action="signup_admin.php?id=<?php echo $id_usuario; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="exampleInputname1">Nome</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($nome_usuario_atualizado); ?>" placeholder="Enter your name" name="username" required>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Email</label>
            <input type="email" class="form-control" value="<?php echo htmlspecialchars($email_usuario); ?>" placeholder="Enter your email" name="email" required>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" id="password" class="form-control" value="<?php echo htmlspecialchars($password_usuario); ?>" placeholder="Enter your password" name="password" required>
            <button type="button" id="password-toggle" class="btn btn-secondary mt-2" onclick="togglePasswordVisibility()">Mostrar senha</button>
        </div>
        <div class="form-group">
            <label for="exampleInputCategory1">Categoria</label>
            <br>
            <select name="category" id="user_type" onchange="showCpfField()" required>
                <option value="user" <?php if ($categoria_usuario == 'user') echo 'selected'; ?>>User</option>
                <option value="admin" <?php if ($categoria_usuario == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="seller" <?php if ($categoria_usuario == 'seller') echo 'selected'; ?>>Seller</option>  
            </select>
        </div>

        <div class="form-group" id="cpf_field" style="display: <?php echo ($categoria_usuario == 'seller') ? 'block' : 'none'; ?>;">
            <label for="exampleInputCpf">CPF</label>
            <input type="text" class="form-control" placeholder="Enter your CPF" name="cpf" value="<?php echo htmlspecialchars($CPF); ?>">
            <div id="cpf-error" class="text-danger"></div> 
        </div>

        <h1>Imagens</h1>
        <br>
        <?php
        $imageSql = "SELECT * FROM imagens WHERE usuario_id = ?";
        $stmt = $con->prepare($imageSql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $imageResult = $stmt->get_result();

        if ($imageResult->num_rows > 0) {
            while ($imageRow = $imageResult->fetch_assoc()) {
                $imageId = $imageRow['id'];
                $imageName = $imageRow['nome_imagem'];
                echo "<div>";
                echo "<img src='../admin/imagens/$id_usuario/$imageName' alt='Imagem do Usuário' id='image_$imageId' width='100' height='100'>";
                echo "<button class='btn btn-danger delete-icon' onclick='confirmDelete($imageId)'>Deletar</button>";
                echo "</div> <br>";
            }
        } else {
            echo "<p>Não há imagens para este usuário.</p>";
        }
        ?>
        <br>
        <h2>Adicionar Imagem</h2>
        <br>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*" required>
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($id_usuario); ?>">
            <br><br>
            <input type="submit" value="Adicionar Imagem" name="upload" class="btn btn-primary forms_botoes">
            <button type="submit" class="btn btn-primary forms_botoes">Update</button>
            <a class="btn btn-primary forms_botoes" href="crud_admin.php">Voltar ao Crud</a>
        </form>
    </div>
    </form>
</div>

<!-- Optional JavaScript -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7f"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>
