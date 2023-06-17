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
    <input type="text" id="email" name="email"><br>
    <input type="submit" name="submit" id="submit" value="Submit">
</form>
<?php
$cant_save = FALSE;
if(isset($_POST['submit'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if (empty($_POST["username"]) || !preg_match("/[a-zA-Z0-9]+/", $username)) {
        $nameErr = "Invalid Username, Email, or Password";
        $cant_save = TRUE;
    }
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (empty($_POST["email"])) {
        echo "Invalid Username, Email, or Password";
        $cant_save = TRUE;
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $cant_save = TRUE;
            echo "Invalid Username, Email, or Password";
        }
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if (empty($_POST["password"])) {
        echo "Invalid Username, Email, or Password";
        $cant_save = TRUE;
    }
    $sql_u = "SELECT * FROM accounts WHERE username='$username'";
    $res_u = mysqli_query($conn, $sql_u);

    if(mysqli_num_rows($res_u) > 0) {
        echo "<br>Invalid Username, Email, or Password";
    } elseif($cant_save) {
        echo '<br>Invalid Username, Email, or Password';
    } else {
        $query = "INSERT INTO accounts (username, email, password) 
      	    	  VALUES ('$username', '$email', '$password')";
        $results = mysqli_query($conn, $query);
        echo 'Saved!';
        exit();
    }
}
?>