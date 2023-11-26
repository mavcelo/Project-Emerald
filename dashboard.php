<?php
// Start a session to manage user authentication
session_start();
unset($_SESSION['attempt']);

// Check if the user is not logged in, redirect to index.php if not logged in
if(!isset($_SESSION['id'])) {
    header("Location: /index.php");
}

// Determine if the user is an admin or a guest
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom stylesheets -->
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="styles/style.css">
  </head>
  <body style="overflow-x: hidden;" class="dashboard">
    <!-- Navigation Bar -->
    <nav class="navbar bg-dark navbar-dark">
        <div class="container-fluid" >
            <a class="navbar-brand" href="/dashboard.php">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav ms-auto"> <!-- Use ms-auto to push the items to the right -->
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content - Cards Section -->
    <div class="row justify-content-center"> <!-- Use justify-content-center to center the cards -->
        <!-- Card 1 - Tournaments -->
        <div class="mt-5 card col m-5" style="width:20rem;">
            <h4 class="card-title mb-4 text-center">Tournaments</h4>
            <img class="card-img" src="img/splash_brand.jpg">
            <p class="card-text p-2 text-center">
                Create tournaments here.
                <p class="card-text p-2 text-center">
                    This is the tournament dashboard you use to create tournaments, generate codes,
                    and organize teams.
                </p>
            </p>
            <!-- Button to go to tournaments page -->
            <div class="p-2 text-center">
                <button class="btn btn-primary btn-sm" onclick='location.href="tournaments.php";'>Go to Tournaments</button>
            </div>
        </div>

        <!-- Card 2 - Stats -->
        <div class="mt-5 card col m-5" style="width:20rem;">
            <h4 class="card-title mb-4 text-center">Stats</h4>
            <img class="card-img" src="img/splash_yi.jpg">
            <p class="card-text p-2 text-center">
                Show off your players here.
                <p class="card-text p-2 text-center">
                    This is the stats dashboard. You can view player stats
                    and download a spreadsheet to show the teams.
                </p>
            </p>
            <!-- Button to go to stats page -->
            <div class="p-2 text-center">
                <button class="btn btn-primary btn-sm" onclick='location.href="stats.php";'>Go to Stats</button>
            </div>
        </div>

        <!-- Card 3 - Players -->
        <div class="mt-5 card col m-5" style="width:20rem;">
            <h4 class="card-title mb-4 text-center">Players</h4>
            <img class="card-img" src="img/splash_zed.jpg">
            <p class="card-text p-2 text-center">
                Add your players to a game.
                <p class="card-text p-2 text-center">
                    This is the players taking place in the current tournament. 
                    Add players to track and generate a tournament code.
                </p>
            </p>
            <!-- Button to go to players page -->
            <div class="p-2 text-center">
                <button class="btn btn-primary btn-sm" onclick='location.href="players.php";'>Go to Players</button>
            </div>
        </div>
    </div>
  </body>
</html>
