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


if (isset($_POST['confirmStats'])) {
    // Retrieve data from the form submission
    $matchId = $_POST['matchId'];
    $playerName = $_POST['playerName'];
    $kills = $_POST['kills'];
    $deaths = $_POST['deaths'];
    $assists = $_POST['assists'];
    $kd = $_POST['kd'];
    $kda = $_POST['kda'];
    $cs = $_POST['cs'];
    $csm = $_POST['csm'];
    $csm = $_POST['dmg'];
    $csm = $_POST['dmm'];
    $vs = $_POST['vs'];
    $kp = $_POST['kp'];

    // Perform the insertion into the player_stats table
    // Use prepared statements to prevent SQL injection

    $stmt = $conn->prepare("INSERT INTO player_stats (`name`, kills, deaths, assists, kd, kad, cs, csm, dmg, dmm, vision_score, kp, match_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiiiiiiiiis", $playerName, $kills, $deaths, $assists, $kd, $kda, $cs, $csm, $dmg, $dmm, $vs, $kp, $matchId);
    
    if ($stmt->execute()) {
        echo "Player stats confirmed and submitted";
    } else {
        echo "Error adding player stats: " . $stmt->error;
    }

    $stmt->close();
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
        <link rel="stylesheet" href="styles/stats.css">
    </head>
    <body style="overflow-x: hidden;">
    <nav class="navbar bg-dark navbar-dark">
            <div class="container-fluid" >
                <a class="navbar-brand" href="/dashboard.php">Dashboard</a>

                <?php if ($guestoradmin == "Admin") {echo '<div class="tab active" onclick=openTab("statGen")>Generate Stats</div>';} ?>
                <?php if ($guestoradmin == "Guest") {echo '<div class="tab active" onclick=openTab("draftOrganization")>Team Stats</div>';} else {echo '<div class="tab" onclick=openTab("draftOrganization")>Team Stats</div>';}?>
                <div class="tab" onclick="openTab('generalStatsView')">Overall Player Stats</div>
                <?php if ($guestoradmin == "Admin") {echo '<div class="tab" onclick=openTab("teamManagement")>Team Management</div>';} ?>
                <!-- <div class="tab" onclick="openTab('teamOrganization')">Team Organization</div>
                <div class="tab" onclick="openTab('teamOrganization')">Team Organization</div> -->


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
    


        
    <div id="tabs">

        <div class="tabContent" id="statGenContent" style=<?php if ($guestoradmin == "Admin") {echo '"display: block;"';} else {echo '"display: none;"';} ?>>
            <form method="post">
                <label for="matchId">Match ID:</label>
                <input type="text" id="matchId" name="matchId" required>
                <br>
                <button name="submit" type="submit">Get Match Data</button>
            </form>

            
            <?php 
            $riotToken = $_SESSION['riotApiKey'];
            // get vds stats here
                if (isset($_POST['submit'])) {
                    $matchId = htmlspecialchars($_POST['matchId']);
                    
                    $results = getMatchData($matchId, $riotToken);
                
                    if (isset($results['error'])) {
                        echo "Error: " . $results['error'];
                    } else {
                        echo "<pre>"; // Use <pre> tag for a more readable output
                        $_SESSION['playerKDA'] = getPlayerMatchStats($matchId, $riotToken, $conn);
                        // print_r($_SESSION['playerKDA'] . "\n");
                        echo "Retrieved data for: \n";
                        foreach ($_SESSION['playerKDA'] as $matchStats) {
                            echo 'Player: ' . $matchStats['PlayerName'] . "\n";
                            
                        }

                        echo "</pre>";
                    }
                }
            
            ?>

            <table class="table table-hover table-striped player-table">
                <tr>
                    <th>Player Name</th>
                    <th>Kills</th>
                    <th>Deaths</th>
                    <th>Assists</th>
                    <th>K/D</th>
                    <th>(K+A)/D</th>
                    <th>CS</th>
                    <th>CS/M</th>
                    <th>Damage</th>
                    <th>DPM</th>
                    <th>Vision Score</th>
                    <th>K/P</th>
                </tr>
                <?php
                    if (isset($_POST['submit'])) {
                        foreach ($_SESSION['playerKDA'] as $matchStats) {
                            
                            echo '<tr>';
                            echo '<td>' . $matchStats['PlayerName'] . "</td>";
                            echo '<td>' . $matchStats['Kills'] . "</td>";
                            echo '<td>' . $matchStats['Deaths'] . "</td>";
                            echo '<td>' . $matchStats['Assists'] . "</td>";
                            echo '<td>' . $matchStats['K/D'] . "</td>";
                            echo '<td>' . $matchStats['K/D/A'] . "</td>";
                            echo '<td>' . $matchStats['CS'] . "</td>";
                            echo '<td>' . $matchStats['CSM'] . "</td>";
                            echo '<td>' . $matchStats['DMG'] . "</td>";
                            echo '<td>' . $matchStats['DMM'] . "</td>";
                            echo '<td>' . $matchStats['VS'] . "</td>";
                            echo '<td>' . $matchStats['KP'] . "%</td>";
                            echo '</tr>';
                    
                            
                            echo '</tr>';
                        }

                    }
                ?>
                
            </table>
            <?php 
            if (isset($_POST['confirmStats'])) {
                echo '<td type="hidden">
                                    <form method="post">
                                        <input type="hidden" name="playerName" value="' . $matchStats['PlayerName'] . '">
                                        <input type="hidden" name="kills" value="' . $matchStats['Kills'] . '">
                                        <input type="hidden" name="deaths" value="' . $matchStats['Deaths'] . '">
                                        <input type="hidden" name="assists" value="' . $matchStats['Assists'] . '">
                                        <input type="hidden" name="kd" value="' . $matchStats['K/D'] . '">
                                        <input type="hidden" name="kda" value="' . $matchStats['K/D/A'] . '">
                                        <input type="hidden" name="cs" value="' . $matchStats['CS'] . '">
                                        <input type="hidden" name="csm" value="' . $matchStats['CSM'] . '">
                                        <input type="hidden" name="dmg" value="' . $matchStats['DMG'] . '">
                                        <input type="hidden" name="dmm" value="' . $matchStats['DMM'] . '">
                                        <input type="hidden" name="vs" value="' . $matchStats['VS'] . '">
                                        <input type="hidden" name="kp" value="' . $matchStats['KP'] . '">
                                    </form>
                                </td>';
               
            
            }
            echo' <form method="post">
                <button type="submit" name="confirmStats" class="btn btn-success">Confirm and Submit Match</button>
            </form>';
            ?>
        </div>


        <div class="tabContent" id="draftOrganizationContent" style=<?php if ($guestoradmin == "Guest") {echo '"display: block;"';} else {echo '"display: none;"';} ?>>
            <!-- Content for the Setup View tab -->
            <h2>Team Stats</h2>

            <table class="table table-hover table-bordered player-table">
                <tr>
                    <th>Player Name</th>
                    <th>Kills</th>
                    <th>Deaths</th>
                    <th>Assists</th>
                    <th>K/D</th>
                    <th>(K+A)/D</th>
                    <th>CS</th>
                    <th>CS/M</th>
                    <th>Damage</th>
                    <th>DPM</th>
                    <th>Vision Score</th>
                    <th>K/P</th>
                </tr>
                <?php
                    if (isset($_POST['submit'])) {
                        foreach ($_SESSION['playerKDA'] as $matchStats) {
                            
                            echo '<tr>';
                            echo '<td>' . $matchStats['PlayerName'] . "</td>";
                            echo '<td>' . $matchStats['Kills'] . "</td>";
                            echo '<td>' . $matchStats['Deaths'] . "</td>";
                            echo '<td>' . $matchStats['Assists'] . "</td>";
                            echo '<td>' . $matchStats['K/D'] . "</td>";
                            echo '<td>' . $matchStats['K/D/A'] . "</td>";
                            echo '<td>' . $matchStats['CS'] . "</td>";
                            echo '<td>' . $matchStats['CSM'] . "</td>";
                            echo '<td>' . $matchStats['DMG'] . "</td>";
                            echo '<td>' . $matchStats['DMM'] . "</td>";
                            echo '<td>' . $matchStats['VS'] . "</td>";
                            echo '<td>' . $matchStats['KP'] . "%</td>";
                            echo '</tr>';
                    
                            
                            echo '</tr>';
                        }

                    }
                ?>
                
            </table>
            
        </div>


        <div class="tabContent col-md-10" id="generalStatsViewContent" style="display: none">
            EMPTY FOR NOW
        </div>

        <div class="tabContent" id="teamManagementContent" style="display: none;">
            <h1>Manage Team Stats</h1>
    
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
