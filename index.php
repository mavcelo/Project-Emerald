<?php include('db_config.php');
?>


<!DOCTYPE html>

<?php $league_name = 'Emerald League'?>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $league_name?> Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
  </head>
  <body>
    <main>
      <?php
          echo '<h1 style="text-align:center"> Hello! Welcome to ' . $league_name . ' </h1';
      ?>  
    </main>
    <h2 style="text-align:center;margin-top:200px;">Login:</h2>
      <br>
      <div class="container mt-3 w3-border w3-padding w3-round ws-grey col-md-2">
        <form action="" method="POST">
          <div>
            <label for="username" class="form-label">Username: </label>
            <input type="text" id="username" class="form-control" placeholder="Enter Username" name="username"><br><br>
          </div>
          <div>
            <label for="password" class="form-label">Password: </label>
            <input type="password" id="password" class="form-control" placeholder="Enter Password" name="password"><br>
          </div>  
          <input type="submit" class="btn btn-secondary my-3" name="submit" value="Submit">
        </form>
        <a href="/register.php">
          <button type="button" class="btn btn-light">Register Here!</button>
        </a>
      </div>
  </body>
</html>

<?php 
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


if(isset($_POST['submit'])) {
  $sql = $conn->prepare("SELECT * FROM accounts WHERE username = ?");
  $sql->bind_param("s", strtolower($username));
  $sql->execute();
  $result = $sql->get_result()->fetch_assoc();
  if ($result && password_verify($_POST['password'], $result['password'])) {
      session_start();
      session_regenerate_id();
      $id = session_id();
      $_SESSION['loggedin'] = TRUE;
      $_SESSION['username'] = strtolower($_POST['username']);
      $_SESSION['id'] = $id;
      if($_SESSION['username'] == 'admin') {
        $_SESSION['isadmin'] = TRUE;
      } else {
        $_SESSION['isadmin'] = FALSE;
      }
      header("Location: /dashboard.php");
      exit();
  } else {
      $error = "Invalid username or password";
      echo $error;
  }
$conn->close();
}


?>


