<?php 
session_start();

if(!isset($_SESSION['id'])) {
    header("Location: /index.php");
}

if($_SESSION['isadmin'] == TRUE) {
    $guestoradmin = 'Admin';
} else {
    $guestoradmin = 'Guest';
}

echo 'hello ' . filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
echo '<br>ID: ' . $_SESSION['id'];
echo '<br> you are: ' . $guestoradmin;
?>

