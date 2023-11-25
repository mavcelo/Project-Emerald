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

    $stmt = $conn->prepare("INSERT INTO player_stats (`name`, kills, deaths, assists, kd, kad, cs, csm, dmg, dmm, vs, kp, match_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiiiiiiiiis", $playerName, $kills, $deaths, $assists, $kd, $kda, $cs, $csm, $dmg, $dmm, $vs, $kp, $matchId);
    
    if ($stmt->execute()) {
        echo "Player stats confirmed and added to the player_stats table.";
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

                <div class="tab active" onclick="openTab('statGen')">Generate Stats</div>
                <div class="tab" onclick="openTab('generalStatsView')">Overall Stats</div>
                <div class="tab" onclick="openTab('draftOrganization')">Draft Stats</div>
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

        <div class="tabContent" id="statGenContent" style="display: block;">
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
                        // print_r($results); // Use print_r to display the array contents
                        $playerName = getPlayerNamesFromMatch($matchId, $riotToken, $conn);
                        $_SESSION['playerKDA'] = getPlayerKDAFromMatch($matchId, $riotToken, $conn);
                        echo "Retrieved data for: \n";
                        foreach ($_SESSION['playerKDA'] as $playerStats) {
                            echo 'Player: ' . $playerStats['PlayerName'] . "\n";
                            
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
                        foreach ($_SESSION['playerKDA'] as $playerStats) {
                            
                            echo '<tr>';
                            echo '<td>' . $playerStats['PlayerName'] . "</td>";
                            echo '<td>' . $playerStats['Kills'] . "</td>";
                            echo '<td>' . $playerStats['Deaths'] . "</td>";
                            echo '<td>' . $playerStats['Assists'] . "</td>";
                            echo '<td>' . $playerStats['K/D'] . "</td>";
                            echo '<td>' . $playerStats['K/D/A'] . "</td>";
                            echo '<td>' . $playerStats['CS'] . "</td>";
                            echo '<td>' . $playerStats['CSM'] . "</td>";
                            echo '<td>' . $playerStats['DMG'] . "</td>";
                            echo '<td>' . $playerStats['DMM'] . "</td>";
                            echo '<td>' . $playerStats['VS'] . "</td>";
                            echo '<td>' . $playerStats['KP'] . "%</td>";
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
                                        <input type="hidden" name="playerName" value="' . $playerStats['PlayerName'] . '">
                                        <input type="hidden" name="kills" value="' . $playerStats['Kills'] . '">
                                        <input type="hidden" name="deaths" value="' . $playerStats['Deaths'] . '">
                                        <input type="hidden" name="assists" value="' . $playerStats['Assists'] . '">
                                        <input type="hidden" name="kd" value="' . $playerStats['K/D'] . '">
                                        <input type="hidden" name="kda" value="' . $playerStats['K/D/A'] . '">
                                        <input type="hidden" name="cs" value="' . $playerStats['CS'] . '">
                                        <input type="hidden" name="csm" value="' . $playerStats['CSM'] . '">
                                        <input type="hidden" name="dmg" value="' . $playerStats['DMG'] . '">
                                        <input type="hidden" name="dmm" value="' . $playerStats['DMM'] . '">
                                        <input type="hidden" name="vs" value="' . $playerStats['VS'] . '">
                                        <input type="hidden" name="kp" value="' . $playerStats['KP'] . '">
                                    </form>
                                </td>';
               
            
            }
            echo' <form method="post">
                <button type="submit" name="confirmStats" class="btn btn-success">Confirm Selected Player Stats</button>
            </form>';
            ?>
        </div>


        <div class="tabContent" id="draftOrganizationContent" style="display: none;">
            <!-- Content for the Setup View tab -->
            <h2>Draft View</h2>
            <p>This is the Draft View content.</p>
            <p>Specific data for Draft View goes here.</p>
            
        </div>


        <div class="tabContent col-md-10" id="generalStatsViewContent" style="display: none">
            EMPTY FOR NOW
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
