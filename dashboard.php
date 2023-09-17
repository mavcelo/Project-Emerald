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
            <a class="navbar-brand">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" style="text-align:right" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="row">
        <div class="mt-5 card col m-5" style="width:20rem;margin:20px 0 24px 0">
            <h4 class="card-title mb-4" style="text-align:center;">Tournaments</h4>
            <img class="card-img" src="img/splash_brand.jpg">
            <p class="card-text p-2">
                Create tournaments here.
                <p class="card-text p-2">
                    This is the tournament dashboard you use to create tournaments, generate codes,
                    and organize teams.
                </p>
            </p>
            <div class="p-2">
                <button class="btn btn-primary btn-sm" onclick='location.href="tournament.php";'>Go to tournament</button>
            </div>
        </div>
        <div class="mt-5 card col m-5" style="width:20rem;margin:20px 0 24px 0">
            <h4 class="card-title mb-4" style="text-align:center;">Stats</h4>
            <img class="card-img" src="img/splash_yi.jpg">
            <p class="card-text p-2">
                Show off your players here.
                <p class="card-text p-2">
                    This is the stats dashboard. You can view player stats
                    and download a spreadsheet to show the teams.
                </p>
            </p>
            <div class="p-2">
                <button class="btn btn-primary btn-sm" href="">Go to stats</button>
            </div>
        </div>
        <div class="mt-5 card col m-5" style="width:20rem;margin:20px 0 24px 0">
            <h4 class="card-title mb-4" style="text-align:center;">Players</h4>
            <img class="card-img" src="img/splash_zed.jpg">
            <p class="card-text p-2">
                Add your players to a game
                <p class="card-text p-2">
                    This is the players taking place in the current tournament. 
                    Here you can add players to track and generate a tournament code.
                </p>
            </p>
            <div class="p-2">
                <button class="btn btn-primary btn-sm" onclick='location.href="players.php";'>Go to players</button>
            </div>
        </div>
    </div>
  </body>
</html>
