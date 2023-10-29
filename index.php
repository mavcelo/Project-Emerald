<?php 
include('db_config.php');
session_start();


// broken, sets to 0 on refresh, not the intended affect, set to 0 to get rid of error for now
$_SESSION['attempt'] = 0;
?>


<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
  </head>
  <body class="login"> 
    <main>
 
    </main>
      <div class="container mt-3 w3-border w3-padding w3-round ws-grey col-md-2" style="padding-top:320px;">
        <form action="" method="POST">
          <div style="padding-bottom:15px;">
            <label for="username" class="form-label">Username: </label>
            <input type="text" id="username" class="form-control" placeholder="Enter Username" name="username"><br>
            <label for="password" class="form-label">Password: </label>
            <input type="password" id="password" class="form-control" placeholder="Enter Password" name="password">
          </div>  
          <input type="submit" class="btn btn-primary my-2" name="submit" value="Submit">
        </form>
        <a href="/register.php">
          <button type="button" class="btn btn-success">Register Here!</button>
        </a>
      </div>
  </body>
</html>

<?php 
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


if(isset($_POST['submit'])) {
  $sql = $conn->prepare("SELECT * FROM accounts WHERE username = ?");
  $username = strtolower($username);
  $sql->bind_param("s", $username);
  $sql->execute();
  $result = $sql->get_result()->fetch_assoc();

  
  $expire_stamp = date('Y-m-d H:i:s', strtotime("+5 min"));

  if ($result && password_verify($_POST['password'], $result['password']) && $_SESSION['attempt'] < 5) {
      
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
    if($_SESSION['attempt'] < 5) {
      $_SESSION['attempt'] = $_SESSION['attempt'] + 1;
      echo "<div style='text-align:center;color:red;margin-top:20px;padding-right:60px;'>Incorrect username or password</div>";
      // echo $_SESSION['attempt'];
      
    } else
      echo "<div style='text-align:center;color:red;margin-top:20px;padding-right:60px;'>You have run out of attempts<br>please try again later.</div>";
      
  }
$conn->close();
}


?>


