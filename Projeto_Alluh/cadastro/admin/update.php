<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location: ../connect&login/login.php');
}

include '../connect&login/connect.php';

$cpf = $cpfError = '';
$displayCPF = 'none';

if (isset($_POST['update'])) {
    $id = $_POST["id"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $category = $_POST["category"];
    $cpf = $_POST["cpf"];

    if (!empty($username) && !empty($password) && !empty($email) && !empty($category)) {
        $sql = "UPDATE registration SET username = ?, password = ?, email = ?, category = ?, cpf = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssssi", $username, $password, $email, $category, $cpf, $id);

        if ($stmt->execute()) {
            if (isset($_POST['hidden_images']) && is_array($_POST['hidden_images'])) {
                foreach ($_POST['hidden_images'] as $imageId) {
                    $deleteImageSql = "DELETE FROM imagens WHERE id = ?";
                    $deleteStmt = $con->prepare($deleteImageSql);
                    $deleteStmt->bind_param("i", $imageId);
                    $deleteStmt->execute();
                }
            }

            echo '<script>alert("Registro atualizado com sucesso.");</script>';
            echo '<script>setTimeout(function(){ window.location.href = "crud_admin.php"; }, 800);</script>';
        } else {
            echo '<script>alert("Falha na atualização: ' . $stmt->error . '");</script>';
        }
        $stmt->close();
    } else {
        echo '<script>alert("Preencha todos os campos obrigatórios.");</script>';
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM registration WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $username = $row['username'];
            $password = $row["password"];
            $email = $row["email"];
            $category = $row["category"];
            $cpf = $row["cpf"];
        }
    } else {
        header('location: crud_admin.php');
        exit;
    }
} else {
    header('location: crud_admin.php');
    exit;
}

if ($category === 'seller') {
    $displayCPF = 'block';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formulário de Atualização de Usuário</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

        .cpf-field {
            display: <?php echo $displayCPF; ?>;
        }

        .error-message {
            color: red;
        }

        .cpf-error-container {
            display: none;
            background-color: #ffdddd;
            padding: 10px;
            border: 1px solid #ff6b6b;
            width: 40ch;
        }

        .cpf-error {
            color: #ff6b6b;
        }
    </style>
</head>
<body>
    <h1 class="Welcome">Bem-vindo admin <?php echo $_SESSION['username']; ?></h1>

    <div class "container">
        <a href="../connect&login/logout.php" class="btn btn-primary mt-5">Logout</a>
        <a href="signup_admin.php" class="btn btn-primary mt-5">SignUp</a>
        <a href="crud_admin.php" class="btn btn-primary mt-5">Crud</a>
    </div>

    <h2>Formulário de Atualização de Usuário</h2>
    <form action="" method="post">
        <fieldset>
            <legend>Informação pessoal:</legend>
            User: <br>
            <input type="text" name="username" value="<?php echo $username; ?>">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <br>
            Email:<br>
            <input type="email" name="email" value="<?php echo $email; ?>">
            <br>
            Password:<br>
            <input type="password" name="password" value="<?php echo $password; ?>">
            <br>
            Category:<br>
            <select name="category" id="user_type">
                <option value="user" <?php if ($category === 'user') echo 'selected'; ?>>User</option>
                <option value="admin" <?php if ($category === 'admin') echo 'selected'; ?>>Admin</option>
                <option value="seller" <?php if ($category === 'seller') echo 'selected'; ?>>Vendedor</option>
            </select>
            <br>
            
            <div class="cpf-field">
                CPF:<br>
                <input type="text" name="cpf" placeholder="000.000.000-00" value="<?php echo $cpf; ?>">
                <div class="cpf-error-container">
                    <div class="cpf-error">CPF inválido. Deve conter 11 dígitos numéricos<br>(ex: 123.456.789-09).</div>
                </div>
            </div>
            
            <br>
            <br>
            <input type="submit" value="Atualizar" name="update" class="btn btn-primary">
        </fieldset>
    </form>

    <script>
        const cpfInput = document.querySelector('input[name="cpf"]');
        const cpfError = document.querySelector('.cpf-error-container');

        cpfInput.addEventListener('input', function () {
            const cpfValue = cpfInput.value;
            const isValid = validateCPF(cpfValue);

            if (!isValid) {
                showCPFError();
            } else {
                hideCPFError();
            }
        });

        function validateCPF(cpf) {
            cpf = cpf.replace(/[^\d]/g, '');
            if (cpf.length !== 11 || !/^\d{11}$/.test(cpf)) {
                return false;
            } else {
                cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                cpfInput.value = cpf;
                return true;
            }
        }

        function showCPFError() {
            cpfError.style.display = 'block';
        }

        function hideCPFError() {
            cpfError.style.display = 'none';
        }

        document.querySelector('form').addEventListener('submit', function (event) {
            if (cpfInput.style.display === 'block' && !validateCPF(cpfInput.value)) {
                event.preventDefault();
                showCPFError();
            }
        });

        document.getElementById('user_type').addEventListener('change', function () {
            const selectedCategory = this.value;
            const cpfField = document.querySelector('.cpf-field');

            if (selectedCategory === 'seller') {
                cpfField.style.display = 'block';
                if (!validateCPF(cpfInput.value)) {
                    showCPFError();
                }
            } else {
                cpfField.style.display = 'none';
                hideCPFError();
            }
        });
    </script>
    
    <h2>Imagens do Usuário</h2>
    <?php
    $imageSql = "SELECT * FROM imagens WHERE usuario_id = ?";
    $stmt = $con->prepare($imageSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $imageResult = $stmt->get_result();

    if ($imageResult->num_rows > 0) {
        while ($imageRow = $imageResult->fetch_assoc()) {
            $imageId = $imageRow['id'];
            $imageName = $imageRow['nome_imagem'];
            echo "<div>";
            echo "<img src='imagens/$id/$imageName' alt='Imagem do Usuário' id='image_$imageId'>";
            echo "<button class='btn btn-danger delete-icon' onclick='toggleDeleteButton(this)'>Hide</button>";
            echo "<input type='hidden' name='hidden_images[]' value='$imageId'>";
            echo "</div>";
        }
    }
    ?>
    
    <script>
        function toggleDeleteButton(button) {
            button.classList.toggle('clicked');
        }
    </script>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*">
        <input type="hidden" name="user_id" value="<?php echo $id; ?>"><br><br>
        <input type="submit" value="Adicionar Imagem" name="upload" class="btn btn-primary">
    </form>
</body>
</html>
