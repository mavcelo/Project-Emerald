<?php 
// ya boi
include('./db_config.php');
session_start();

?>
<h1 style="text-align:center;margin-top:50px;">Register</h1>
<form method="POST" style="text-align:center;padding:10px;margin-top:300px;">
    <label for="username" type="text">Username: </label>
    <input type="text" id="username" name="username"> <br>
    <label for="password" type="password">Password: </label>
    <input type="password" id="password" name="password"> <br>
    <label for="email" type="text">Email: </label>
    <input type="text" id="email" name="email"><br>
    <input type="submit" name="submit" id="submit" value="Submit">
</form>
<?php
$login_err = "<div style='text-align:center;'>Invalid Username, Email, or Password</div>";
$cant_save = FALSE;
if(isset($_POST['submit'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql_u = "SELECT * FROM accounts WHERE username='$username'";
    $res_u = mysqli_query($conn, $sql_u);
    
    if (empty($_POST["username"]) || !preg_match("/[a-zA-Z0-9]+/", $username)) {
        echo $login_err;
        $cant_save = TRUE;
    } elseif (empty($_POST["email"])) {
        echo $login_err;
        $cant_save = TRUE;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $cant_save = TRUE;
        echo $login_err;
    } elseif (empty($_POST["password"])) {
        echo $login_err;
        $cant_save = TRUE;
    } elseif(mysqli_num_rows($res_u) > 0) {
        echo $login_err;
    } elseif($cant_save) {
        echo $login_err;
    } else {
        $query = "INSERT INTO accounts (username, email, password) 
      	    	  VALUES ('$username', '$email', '$password')";
        $results = mysqli_query($conn, $query);
        echo '<div style="text-align:center">Saved!</div>';
        exit();
    }
}
?>