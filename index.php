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
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
  </head>
  <body>
    <main>
        <?php
            echo '<h1 style="text-align:left"> hello! Welcome to ' . $league_name . ' </h1';
        ?>  
    </main>
	<script src="index.js"></script>
  <h2 style="text-align:center;">Login:</h2>
  <div style=";background-color:#59db42;margin:auto;margin-left:200px;margin-right:200px;text-align:center;height: 500px">
  <br>
  <form action="" method="POST">
    <label for="username">Username: </label>
    <input type="text" id="username" name="username"><br><br>

    <label for="password" style="padding-left:5px">Password: </label>
    <input type="password" id="password" name="password"><br>

    <input type="submit" name="submit" value="Submit">
  </form>
  <a href="/register.php">Register Here!</a>
  </div>  
<?php 

?>
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

}


?>


