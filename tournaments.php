<?php
session_start();
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


/*
NOTES: 

use riot CLIENT api to grab names on player join in lobby. possibly use 
async function if that exists in php? or implement JS listener for player join events and add the player to 
player list. IF not possible, add the players to play in other area on dashboard

*/

function countPlayers($dbConnection) {
    // Define the table name
    $tableName = "players"; // Replace with your actual table name

    // SQL query to count the number of players in the table
    $sql = "SELECT COUNT(*) as playerCount FROM $tableName";

    // Execute the query
    $result = mysqli_query($dbConnection, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        // Check if there is a result
        if ($row) {
            $playerCount = $row['playerCount'];
            return $playerCount;
        }
    }

    // If an error occurs or no players are found, return 0
    return 0;
}



// Function to populate the div with data for a specific role
function populateRoleDiv($conn, $role) {
    // Retrieve data from the database for the specified role
    $sql = "SELECT name, `rank`, role_preferred FROM players WHERE role_preferred = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $role);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<div class='col-md-2 pr-2 role-div'>";
        echo "<div class='table-container'>";
        echo "<table class='table table-hover table-striped' id='table-$role'>";
        echo "<thead>
                <th>Name</th>
                <th>Rank</th>
                <th>Role</th>
            </thead>";
        echo "<tbody>";


        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            $user = $row["name"];
            $url = "'https://www.op.gg/summoners/na/".urlencode($user)."'";
            echo "
                <td value='$user' onclick=location.href=$url>$user</td>
            ";
            echo "<td>" . $row["rank"] . "</td>";
            echo "<td>" . $row["role_preferred"] . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='col-md-2 pr-2 role-div'>";
        echo "<div class='table-container'><table><thead><th>No data found for $role</th></thead></table></div>";
        echo "</div>";
    }
}

function calculateGameInfo($conn) {
    // Define the table name where player data is stored
    $tableName = "players"; // Replace with your actual table name

    // SQL query to retrieve player names, timestamps, and roles, sorted by timestamp in ascending order
    $sql = "SELECT name, time_registered, role_preferred FROM $tableName ORDER BY time_registered ASC";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $players = array();
        $teams = array();
        $remainingPlayers = array();
        $lastTimestamp = null;

        // Initialize role counts
        $roleCounts = [
            'adc' => 0,
            'top' => 0,
            'mid' => 0,
            'jungle' => 0,
            'support' => 0,
        ];

        // Fetch and organize the data
        while ($row = mysqli_fetch_assoc($result)) {
            $players[] = $row; // Store player data
            $lastTimestamp = $row['time_registered']; // Update the last timestamp

            // Increment role counts
            $role = strtolower($row['role_preferred']);
            if (array_key_exists($role, $roleCounts)) {
                $roleCounts[$role]++;
            }

            // Form teams of 5 with the earliest timestamps
            if (count($players) >= 5) {
                $team = array_slice($players, 0, 5); // Get the first 5 players
                $teams[] = $team; // Store the team
                $players = array_slice($players, 5); // Remove the team members from the list
            }
        }

        // Remaining players with the latest timestamps
        $remainingPlayers = $players;

        // Check if there are any remaining players
        $hasRemainingPlayers = !empty($remainingPlayers);

        // Count of extra players
        $extraPlayersCount = count($remainingPlayers);

        // Count of possible teams
        $possibleTeamsCount = count($teams);

        // Return teams, remaining players, last timestamp, role counts, boolean value, extra players count, and possible teams count
        return array(
            'teams' => $teams,
            'remainingPlayers' => $remainingPlayers,
            'lastTimestamp' => $lastTimestamp,
            'roleCounts' => $roleCounts,
            'hasRemainingPlayers' => $hasRemainingPlayers,
            'extraPlayersCount' => $extraPlayersCount,
            'possibleTeamsCount' => $possibleTeamsCount,
        );
    }

    // If an error occurs or no player data is found, return an empty result
    return array(
        'teams' => array(),
        'remainingPlayers' => array(),
        'lastTimestamp' => null,
        'roleCounts' => [],
        'hasRemainingPlayers' => false,
        'extraPlayersCount' => 0,
        'possibleTeamsCount' => 0,
    );
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

                <div class="tab active" onclick="openTab('signupView')">Signup</div>
                <div class="tab" onclick="openTab('draftOrganization')">Draft</div>
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
            

            <div id="draftOrganizationContent" class="tabContent" style="display: none;">
                <!-- Content for the Setup View tab -->
                <h2>Draft View</h2>
                <p>This is the Draft View content.</p>
                <p>Specific data for Draft View goes here.</p>
            </div>
            


            <div class="tabContent" id="signupViewContent" style="display: block">
                <div class="col-md-7" style="margin: auto; margin-top: 40px;">
                    <table class="table table-striped ">
                        <tr>
                            <th>Player Count</th>
                            <th>Possible Teams</th>
                            <th>Extra Players</th>
                            <th>Top Players</th>
                            <th>Jungle Players</th>
                            <th>Mid Players</th>
                            <th>Adc Players</th>
                            <th>Support Players</th>
                        </tr>
                        <tr>
                            <td><?php echo countPlayers($conn); ?></td>
                            <td><?php $data = calculateGameInfo($conn); echo $data['possibleTeamsCount'];?></td>
                            <td>
                                <?php 
                                $data = calculateGameInfo($conn);
                                if ($data['hasRemainingPlayers']) {
                                    if ($data['extraPlayersCount'] > 1) {
                                        echo $data['extraPlayersCount'] . " extra players";
                                    } else {
                                        echo $data['extraPlayersCount'] . " extra player";
                                    }
                                } else {
                                    echo 'No extra players';
                                }
                            
                                ?>
                            </td>
                            <td><?php $data = calculateGameInfo($conn); echo $data['roleCounts']['top'];?></td>
                            <td><?php $data = calculateGameInfo($conn); echo $data['roleCounts']['jungle'];?></td>
                            <td><?php $data = calculateGameInfo($conn); echo $data['roleCounts']['mid'];?></td>
                            <td><?php $data = calculateGameInfo($conn); echo $data['roleCounts']['adc'];?></td>
                            <td><?php $data = calculateGameInfo($conn); echo $data['roleCounts']['support'];?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="container-fluid justify-content-between align-items-end" id="signupViewContentTables" style="display: flex">
                <?php
                $roles = array("top", "jungle", "mid", "adc", "support");
                foreach ($roles as $role) {
                    populateRoleDiv($conn, $role); // Call the function to populate the div for each role
                }
                ?>
                </div>                
            </div>
        </div>


    </body>
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

</html>