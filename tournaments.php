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


/*
NOTES: 

use riot CLIENT api to grab names on player join in lobby. possibly use 
async function if that exists in php? or implement JS listener for player join events and add the player to 
player list. IF not possible, add the players to play in other area on dashboard

*/



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
                        <a class="nav-link" style="text-align:right" href="stats.php">Stats</a>
                        <a class="nav-link" style="text-align:right" href="players.php">Players</a>
                        <a class="nav-link" style="text-align:right" href="logout.php">Logout</a>
                     </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Div that holds the table for the username entries-->
    <div class="ms-4 mt-3 w3-border w3-round ws-grey col-md-4" id="users">
    Select League of Legends usernames to check:<br>
      <select class="col-md-3 form-select" style="height:150px" id="summonerNames" multiple>
        <?php 
        // Loops through the session array of users to securely get the users from the players page entry 
          foreach ($_SESSION['user_list'] as $user) {
            echo "<option value='$user'>$user</option>";
          }
        ?>
      </select>

      <button onclick="checkLobby()" class="btn btn-secondary my-3">Check Lobby</button>
      <p id="lobbyStatus"></p>
    </div>

    <script>
        // Function to check the lobby status for selected usernames
        function checkLobby() {
            var selectedUsernames = Array.from(document.getElementById("summonerNames").selectedOptions).map(option => option.value);
            
            selectedUsernames.forEach(function(summonerName) {
                var apiUrl = 'https://api.example.com/lol/checklobby?summonerName=' + summonerName; // Replace with the actual API endpoint
                
                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    success: function(data) {
                        if (data.inLobby) {
                            $('#lobbyStatus').append(summonerName + ' is in a League of Legends lobby.<br>');
                            
                            // Append the username to a text file
                            $.ajax({
                                url: 'append_username.php', // Replace with the actual PHP script to append to the file
                                method: 'POST',
                                data: {username: summonerName},
                                success: function(response) {
                                    console.log(response);
                                },
                                error: function() {
                                    console.error('Error appending username to file.');
                                }
                            });
                        } else {
                            $('#lobbyStatus').append(summonerName + ' is not in a League of Legends lobby.<br>');
                        }
                    },
                    error: function() {
                        $('#lobbyStatus').append('Error checking lobby status for ' + summonerName + '.<br>');
                    }
                });
            });
        }
    </script>


<?php 
// Loops through the session array of users to securely get the users from the players page entry 
function tablePop() {
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
}

?>


            <div class="col-md-2 pr-2 float-left" id="slottable" style="margin-top: 250px;margin-left:120px;padding-left:10px">
                <table class="table table-hover pr-2" style="height:150px" id="summonerNames" multiple>
                    <thead>
                        <th>Name</th>
                        <th>Rank</th>
                        <th>Role</th>
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
            <div class="col-md-2 pr-2 float-left pl-10" id="slottable" style="margin-top: 250px;padding-left:10px">
                <table class="table table-hover" style="height:150px;padding-right:30px;" id="summonerNames" multiple>
                    <thead>
                        <th>Name</th>
                        <th>Rank</th>
                        <th>Role</th>
                    </thead>
                    <tbody>
                        <?php tablePop();?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-2 pr-2 float-left pl-10" id="slottable" style="margin-top: 250px;padding-left:10px">
                <table class="table table-hover" style="height:150px" id="summonerNames" multiple>
                    <thead>
                        <th>Name</th>
                        <th>Rank</th>
                        <th>Role</th>
                    </thead>
                    <tbody>
                        <?php tablePop();?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-2 mr-2 float-left pl-10" id="slottable"style="margin-top: 250px;padding-left:10px">
                <table class="table table-hover" style="height:150px" id="summonerNames" multiple>
                    <thead>
                        <th>Name</th>
                        <th>Rank</th>
                        <th>Role</th>
                    </thead>
                    <tbody>
                        <?php tablePop();?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-2 mr-2 float-left pl-10" id="slottable" style="margin-top: 250px;padding-left:10px">
                <table class="table table-hover" style="height:150px" id="summonerNames" multiple>
                    <thead>
                        <th>Name</th>
                        <th>Rank</th>
                        <th>Role</th>
                    </thead>
                    <tbody>
                        <?php tablePop();?>
                    </tbody>
                </table>
            </div>

    </body>
</html>