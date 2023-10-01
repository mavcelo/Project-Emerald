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
?>
    
<!DOCTYPE html>
<html lang="en">
    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body style="overflow-x: hidden;">
        <nav class="navbar bg-dark navbar-dark">
            <div class="container-fluid" >
                <a class="navbar-brand" href="/dashboard.php">Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" style="text-align:right" href="tournaments.php">Tournaments</a>
                            <a class="nav-link" style="text-align:right" href="players.php">Players</a>
                            <a class="nav-link" style="text-align:right" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    
    
    
    
    
    
    
        <div class="ms-4 mt-3 w3-border w3-round ws-grey col-md-4" id="users">
            <table class="table table-hover" style="height:150px" id="summonerNames" multiple>
                <thead>
                    <th>Username</th>
                    <th>
                </thead>
                <tbody>
                    <?php 
                    // Loops through the session array of users to securely get the users from the players page entry 
                    if (isset($_SESSION['user_list'][0])) {
                        foreach ($_SESSION['user_list'] as $user) {
                            $url = "'https://www.op.gg/summoners/na/".urlencode($user)."'";
                            echo "
                            <tr>
                                <td value='$user' onclick=location.href=$url>$user</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td>No Players Found</tr></td>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </body>