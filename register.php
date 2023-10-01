<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<?php 
// ya boi
include('./db_config.php');

?>
<h1 style="text-align:center;margin-top:50px;">Register</h1>
<div style='text-align:center;margin-top:200px;'>When selecting a password please ensure it to be 8 characters or longer
      <br>and contain no spaces</div>
<form method="POST" style="text-align:center;padding:10px;margin-top:50px;">
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
    $sql_u = $conn->prepare("SELECT * FROM accounts WHERE username = ?");
    $sql_u->bind_param("s", $username);
    $sql_u->execute();
    $sql_u->store_result();
    
    if (empty($username) || !preg_match("/[a-zA-Z0-9]+/", $username)) {
        echo $login_err;
        $cant_save = TRUE;
    } elseif (strpos($_POST["username"], " ") == TRUE) {
        echo "<div style='text-align:center;'>Whitespaces not allowed</div>";
        $cant_save = TRUE;
    } elseif (empty($_POST["email"])) {
        echo $login_err;
        $cant_save = TRUE;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $cant_save = TRUE;
        echo $login_err;
    } elseif (empty($_POST["password"]) || strlen($_POST['password']) < 8) {
        echo $login_err;
        $cant_save = TRUE;
    } elseif($sql_u->num_rows > 0) {
        echo "<div style='text-align:center;'>Username Taken</div>";
        echo $sql_u->num_rows;
    } elseif($cant_save) {
        echo $login_err;
    } else {
        $query = $conn->prepare("INSERT INTO accounts (username, email, password) 
      	    	  VALUES (?, ?, ?)");
        $query->bind_param("sss", strtolower($username), $email, $password);
        $query->execute();
        echo '<div style="text-align:center">Saved!</div>';
        header("Location: /index.php");
        exit();
    }
}
$conn->close()
?>