<?php
session_start();
include('./requester.php');

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
    
        <form method="post">
            <label for="matchId">Match ID:</label>
            <input type="text" id="matchId" name="matchId" required>
            <br>
            <label for="riotToken">Riot Token:</label>
            <input type="text" id="riotToken" name="riotToken" required>
            <br>
            <button name="submit" type="submit">Get Match Data</button>
        </form>

        <?php 
        if(isset($_POST['submit'])) {
            $results = getMatchData(htmlspecialchars($_POST['matchId']), htmlspecialchars($_POST['riotToken']));
            $data = json_encode($results, JSON_PRETTY_PRINT);
            header('Content-Type: application/json');
            echo "<div>";
            echo $data;
            echo "</div>";
        }
        
        ?>
    </body>