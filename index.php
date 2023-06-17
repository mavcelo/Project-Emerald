<?php include('db_config.php'); ?>

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
    <input type="submit" value="Submit">
  </form>
  <a href="/register.php">Register Here!</a>
  </div>  
<?php 

?>
</body>
</html>


