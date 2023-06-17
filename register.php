<?php 

include('./db_config.php');
session_start();




?>
<form method="POST">
    <label for="username" type="text">Username: </label>
    <input type="text" id="username" name="username"> <br>
    <label for="password" type="password">Password: </label>
    <input type="password" id="password" name="password"> <br>
    <label for="email" type="text">Email: </label>
    <input type="text" id="email" name="email">
    <input type="submit" name="submit" id="submit" value="Submit">
</form>
<?php

if(isset($_POST['submit'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql_u = "SELECT * FROM accounts WHERE username='$username'";
    $res_u = mysqli_query($conn, $sql_u);

    if(mysqli_num_rows($res_u) > 0) {
        echo 'Taken!';
    } else {
        $query = "INSERT INTO accounts (username, email, password) 
      	    	  VALUES ('$username', '$email', '$password')";
        $results = mysqli_query($conn, $query);
        echo 'Saved!';
        exit();
    }
}
?>