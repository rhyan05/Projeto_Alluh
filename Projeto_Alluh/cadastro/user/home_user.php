<?php
session_start();
if(!isset($_SESSION['email'])){#se ele nao esta logado, va para a tela de login
    header('location:login.php');
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Home</title>
  </head>
  <body>
    <h1 class="Welcome">Welcome 
    <?php echo $_SESSION['username'];  ?>
    </h1> 

    <!-- <h1 class="text-center text-warning mt-5">Welcome 

    </h1> 
    os dois modos de fazer vai da certo, um colocando a class ja com as funçoes do css e a outra e atribuindo uma class para ai sim mexer no css-->

    <!-- eu posso trocar essa div por um botao tbm -->
    <div class="container">
        <a href="../connect&login/logout.php" class="btn btn-primary mt-5">Logout</a>
    </div>



  </body>
</html>
<style>
.Welcome{
    text-align: center;
    color:darkblue;
    margin-top: 5px;
}

</style>