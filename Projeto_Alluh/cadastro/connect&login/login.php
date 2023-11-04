<?php
$login = 0;
$invalid = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'connect.php';
    $password = $_POST['password'];
    $email = $_POST['email'];

    $sql = "SELECT * FROM registration WHERE email='$email' AND password='$password'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $row = mysqli_fetch_assoc($result);
            $email = $row['email'];
            $username = $row['username'];

            session_start();
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $username;

            // Defina a categoria aqui após o login -> comum e o valor do usuario.
            $_SESSION['category'] = $row['category'] ?? 'comum'; 

            header('location: verify.php');
            exit;
        } else {
            $invalid = 1;
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
      <!--<link rel="stylesheet" href="/cadastro/sign.css?v=<?php echo time(); ?>">-->
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

      <title>Login</title>
    </head>
    <body>
  <!--Alerts-->
    <?php
      if($login){
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success</strong>You are Logged.
      </div>';
      }

      ?>
      
    <?php
      if($invalid){
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Invalid</strong>User not cadastrad.
      </div>';
      }

      ?>
      <h1 class="Sign_up">Login</h1>
      <div class="container">
          <form action="login.php" method="post">
          <div class="form-group">
                  <label for="exampleInputemail1">email</label>
                  <input type="email" class="form-control"  placeholder="enter your email" name="email">
              </div>

              <div class="form-group">
                  <label for="exampleInputPassword1">Password</label>
                  <input type="password" class="form-control"  placeholder="enter your password" name="password">
              </div>

              <button type="submit" class="btn btn-primary">Login</button>
              <a class="btn btn-primary" href="sign.php">Cadastro</a>
              <a class="btn btn-primary" href="sign_vendedor.php">Cadastro Vendedor</a>
          </form>
  
      </div>

      <!-- Optional JavaScript -->
      <!-- jQuery first, then Popper.js, then Bootstrap JS -->
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
  </html>

  <style>
    <?php include "sign.css" ?>
    
  </style>