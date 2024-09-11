<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location: login.php');
    exit;
}

// Verificar se é um admin ou um usuário comum
if (isset($_SESSION['category'])) {
    $category = $_SESSION['category'];

    if ($category == 'comum') {
        // Usuário comum
        header('location: ../user/home_user.php'); // C página do usuário comum
        exit;
    } elseif ($category == 'admin') {
        // Admin
        header('location: ../admin/home_admin.php'); // C página de admin
        exit; 
    } elseif ($category == 'seller'){
        header('location: ../Vendendor/home_vendedor.php');// C pagina do vendedor
    }else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Falha, categoria não existe</strong> Aguarde 2 segundos.
      </div>';
        header('refresh:2;url=../connect&login/login.php'); 
        exit; 
    }
}
?>
