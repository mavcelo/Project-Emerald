<?php
session_start();
include('./requester.php');
include('./db_config.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        <link rel="stylesheet" href="styles/tournament.css">
    </head>
    <body style="overflow-x: hidden;">
    <nav class="navbar bg-dark navbar-dark">
            <div class="container-fluid" >
                <a class="navbar-brand" href="/dashboard.php">Dashboard</a>

                <div class="tab active" onclick="openTab('generalStatsView')">Overall Stats</div>
                <div class="tab" onclick="openTab('draftOrganization')">Draft Stats</div>
                <!-- <div class="tab" onclick="openTab('teamOrganization')">Team Organization</div>
                <div class="tab" onclick="openTab('teamOrganization')">Team Organization</div> -->


                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" style="text-align:right" href="stats.php">Stats</a>
                            <a class="nav-link" style="text-align:right" href="players.php">Players</a>
                            <a class="nav-link" style="text-align:right" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    


        
    <div id="tabs">

        <div id="content">
            <div id="draftOrganizationContent" class="tabContent" style="display: none;">
            <!-- Content for the Setup View tab -->
            <h2>Draft View</h2>
            <p>This is the Draft View content.</p>
            <p>Specific data for Draft View goes here.</p>
            
            </div>

        </div>

        <div class="tabContent" id="generalStatsViewContent" style="display: block">
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
            // get vds stats here
                if (isset($_POST['submit'])) {
                    $matchId = htmlspecialchars($_POST['matchId']);
                    $riotToken = htmlspecialchars($_POST['riotToken']);
                    
                    $results = getMatchData($matchId, $riotToken);
                
                    if (isset($results['error'])) {
                        echo "Error: " . $results['error'];
                    } else {
                        echo "<pre>"; // Use <pre> tag for a more readable output
                        // print_r($results); // Use print_r to display the array contents
                        $playerName = getPlayerNamesFromMatch($matchId, $riotToken, $conn);
                        print_r($playerName);

                        echo "Kills: " . $results['info']['participants']['0']['kills'] . "\n";
                        echo "Deaths " . $results['info']['participants']['0']['deaths'] . "\n";
                        echo "Assists: " . $results['info']['participants']['0']['assists'] . "\n";
                        echo "</pre>";
                    }
                }
            
            ?>
        </div>
    </div>
        
    <script>
        // Function to open a tab and display its content
        function openTab(tabName) {
            var i;
            var tabs = document.getElementsByClassName("tab");
            var tabContent = document.getElementsByClassName("tabContent");

            // Hide all tab content
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }

            // Remove the 'active' class from all tabs
            for (i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove("active");
            }

            // Display the selected tab content and mark the tab as active
            var tabContentElement = document.getElementById(tabName + "Content");
            tabContentElement.style.display = "block";
            event.currentTarget.classList.add("active");

            // Log the value of tabContentElement to the console
        }
    </script>
    </body>
</html>
