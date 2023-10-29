<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// php spreadsheet library for speadsheet processing
require 'vendor/autoload.php';
include('./db_config.php');
include('./requester.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();

function relax() {
    ;
}

function useRegex($input) {
    $regex = '/([A-Za-z0-9]+( [A-Za-z0-9]+)+)/i';
    return preg_match($regex, $input);
}

date_default_timezone_set('US/Eastern');


if(!isset($_SESSION['id'])) {
    header("Location: /index.php");
}

// check if user is admin
if(isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == TRUE) {
    $guestoradmin = 'Admin';
} else {
    $guestoradmin = 'Guest';
    
    http_response_code(403);
    die('<h2 style="color:red">Forbidden</h2>');   
    
}

// Check if the user list array exists in the session, if not, create it
if (!isset($_SESSION['user_list'])) {
    $_SESSION['user_list'] = array();
}

// Check if the form is submitted to add a user
if (isset($_POST['add_user'])) {
    $readTermsValue = "yes";
    $agreeToTermsValue = "yes";
    $teamCaptainValue = "maybe";
    $rankPrevValue = "N/A";
    $roleAltValue = "Fill";

    // Get the user's name from the form, and sanitize
    $username = htmlspecialchars(strip_tags($_POST['user_name']));
    if ($_POST['api_key'] == "Optional") {
        $rank = htmlspecialchars(strip_tags("Need Verify"));
    } else {
        $api_key = htmlspecialchars(strip_tags($_POST['api_key']));
        $rank = getSummonerRank($username, $api_key);
        if (strpos($rank, 'Error') === 0) {
            $rank = htmlspecialchars(strip_tags("Need Verify"));
        } elseif (strpos($rank, 'fetching') === 0){
            echo "no games played";
        } else {
            relax();
        }
    }
    if (strlen($_POST['role']) > 7) {
        relax();
    } else {
        $rolePref = htmlspecialchars(strip_tags($_POST['role']));
    }

    $discordName = "N/A";

    // Insert the new user into the database
    $timestamp = date('n/j/Y H:i:s', time());
    $sql = "INSERT INTO players (time_registered, read_terms, agree_to_terms, discord_name, team_captain, name, `rank`, rank_previous, role_preferred, role_alternative) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssssssss", $timestamp, $readTermsValue, $agreeToTermsValue, $discordName, $teamCaptainValue, $username, $rank, $rankPrevValue, $rolePref, $roleAltValue);

        try {
            $stmt->execute();
            $_SESSION['user_list'][] = $username;

        } catch (Exception $e) {
            echo "<span style='color:red'>Character Length too long</span>";
        }
        
        $stmt->close();
        $stmt = null;
        gc_collect_cycles();
    } else {
        echo "Error preparing SQL statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();

    // Add the new user to the session array
}


// Check if the form is submitted to remove selected users
if (isset($_POST['remove_selected'])) {
    // Get the selected users to remove
    if (isset($_POST['selected_users'])) {
        $selectedUsers = $_POST['selected_users'];

        foreach ($selectedUsers as $user) {
            $userToRemove = htmlspecialchars(strip_tags($user));
            
            // Remove the user from the session array
            $index = array_search($userToRemove, $_SESSION['user_list']);
            if ($index !== false) {
                unset($_SESSION['user_list'][$index]);
                // Reset array keys to ensure it's sequential
                $_SESSION['user_list'] = array_values($_SESSION['user_list']);
            }
            
            // Remove the user from the database
            $stmt = $conn->prepare("DELETE FROM players WHERE name = ?");
            $stmt->bind_param("s", $userToRemove);
            
            if ($stmt->execute()) {
                // User removed from the database
                relax();
            } else {
                echo "Error removing user from the database: " . $stmt->error;
            }
            
            $stmt->close();
        }

        // Close the database connection
    }
}



// iterate through the rows
if (isset($_FILES['user_file']) && $_FILES['user_file']['error'] === UPLOAD_ERR_OK) {
    echo $_POST['api_key'];
    try {
        // Get the uploaded file
        $file = $_FILES['user_file']['tmp_name'];

        // Load the spreadsheet using PhpSpreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

        // Get the first worksheet
        $worksheet = $spreadsheet->getActiveSheet();

        $isheader = 0;

        // Prepare the SQL statement for inserting data into the database
        $insertQuery = "INSERT INTO players (time_registered, read_terms, agree_to_terms, discord_name, team_captain, name, `rank`, rank_previous, role_preferred, role_alternative) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        if ($_POST['api_key'] == 'Optional') {
            echo '<span style="color:orange">An uploaded rank was unable to be set. API key not found for auto-correction</span>';
        }

        if (!$insertStmt) {
            error_log("Error preparing SQL statement: " . $conn->error);
        } else {

            $insertStmt->bind_param("ssssssssss", $timestamp, $readTerms, $agreeToTerms, $discordName, $teamCaptain, $ign, $rank, $rankPrev, $rolePref, $roleAlt);
            
            // Iterate through the rows
            foreach ($worksheet->getRowIterator() as $row) {
                if ($isheader > 0) {
                    // Get the values for each column
                    $sheetTimestamp = $worksheet->getCellByColumnAndRow(1, $row->getRowIndex())->getValue();
                    $readTerms = $worksheet->getCellByColumnAndRow(2, $row->getRowIndex())->getValue();
                    $agreeToTerms = $worksheet->getCellByColumnAndRow(3, $row->getRowIndex())->getValue();
                    $discordName = $worksheet->getCellByColumnAndRow(4, $row->getRowIndex())->getValue();
                    $teamCaptain = $worksheet->getCellByColumnAndRow(5, $row->getRowIndex())->getValue();
                    $ign = $worksheet->getCellByColumnAndRow(6, $row->getRowIndex())->getValue();
                    $rank = $worksheet->getCellByColumnAndRow(7, $row->getRowIndex())->getValue();
                    $rankPrev = $worksheet->getCellByColumnAndRow(8, $row->getRowIndex())->getValue();
                    $rolePref = $worksheet->getCellByColumnAndRow(9, $row->getRowIndex())->getValue();
                    $roleAlt = $worksheet->getCellByColumnAndRow(10, $row->getRowIndex())->getValue();

                    // Check for null values before using htmlspecialchars
                    $readTerms = ($readTerms !== null) ? htmlspecialchars($readTerms) : null;
                    $agreeToTerms = ($agreeToTerms !== null) ? htmlspecialchars($agreeToTerms) : null;
                    $discordName = ($discordName !== null) ? htmlspecialchars($discordName) : null;
                    $teamCaptain = ($teamCaptain !== null) ? htmlspecialchars($teamCaptain) : null;
                    $ign = ($ign !== null) ? htmlspecialchars($ign) : null;
                    $rank = ($rank !== null && !is_null($rank) && strlen($rank) <= 11) ? htmlspecialchars($rank) : "Need Verify";
                    $rankPrev = ($rankPrev !== null) ? htmlspecialchars($rankPrev) : null;
                    $rolePref = ($rolePref !== null) ? htmlspecialchars($rolePref) : null;
                    $roleAlt = ($roleAlt !== null) ? htmlspecialchars($roleAlt) : null;

                    if (!is_null($rank)) {
                        $rank = useRegex($rank);
                    } else {
                        $rank = "Need Verify";
                    }
                    
                    if (!is_null($rankPrev)) {
                        $rankPrev = useRegex($rankPrev);
                    } else {
                        $rankPrev = "Need Verify";
                    }

                    if ($rank > 30 && !is_null($rank)) {
                        if (isset($_POST['api_key'])) {
                            if ($_POST['api_key'] == "Optional") {
                                $rank = "Need Verify";
                            } else {
                                $api_key = htmlspecialchars(strip_tags($_POST['api_key']));
                                $rank = getSummonerRank($ign, $api_key);
                                if (strpos($rank, 'Error') === 0) {
                                    $rank = "Need Verify";
                                } elseif (strpos($rank, 'fetching') === 0){
                                    $rank = "Need Verify";
                                } else {
                                    relax();
                                }
                            }
                        }

                        $rank = "Need Verify";
                    } else {
                        $rank = "Need Verify";
                    }

                    if ($rankPrev > 30 && !is_null($rankPrev)) {
                        if (isset($_POST['api_key'])) {
                            if ($_POST['api_key'] == "Optional") {
                                $rankPrev = "Need Verify";
                            } else {
                                $api_key = htmlspecialchars(strip_tags($_POST['api_key']));
                                $rankPrev = getSummonerRank($ign, $api_key);
                                if (strpos($rankPrev, 'Error') === 0) {
                                    $rankPrev = "Need Verify";
                                } elseif (strpos($rankPrev, 'fetching') === 0){
                                    echo "no games played";
                                    $rankPrev = "Need Verify";
                                } else {
                                    relax();
                                }
                            }
                        }

                        $rankPrev = "Need Verify";
                    } else {
                        $rankPrev = "Need Verify";
                    }

                    // Process the timestamp
                    $unixTimestamp = ($sheetTimestamp - 25569) * 86400;
                    $dateTime = DateTime::createFromFormat('U.u', $unixTimestamp);
                    if ($dateTime !== false) {
                        // Format the DateTime as a human-readable date and time
                        $timestamp = $dateTime->format('n/j/Y H:i:s');
                    } else {
                        relax();
                    }

                    // Skip rows with empty ign, rank, or role
                    if (!empty($ign) && !empty($rank) && !empty($rolePref)) {
                        // Check if the user already exists in the database
                        $checkQuery = "SELECT name FROM players WHERE name = ?";
                        $checkStmt = $conn->prepare($checkQuery);
                        $checkStmt->bind_param("s", $ign);
                        $checkStmt->execute();
                        $checkStmt->store_result();

                        if ($checkStmt->num_rows == 0) {
                            // Execute the insert statement
                            if ($insertStmt->execute()) {
                                relax();
                            } else {
                                error_log("Error executing SQL statement: " . $insertStmt->error);
                            }
                        } else {
                            relax();
                        }

                        $checkStmt->close();
                    }
                } else {
                    $isheader = 1;
                }
            }
        }

        // Close the insert statement
        $insertStmt->close();
    } catch (Exception $e) {
        // Log the exception to a file or database
        error_log("An error occurred: " . $e->getMessage());
        echo "An error occurred while processing the file. Please try again later.";
        echo '<br>' . $e;
    }
}




    




?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie-edge">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./style.css">
    <style>
        /* Add custom styles here  */
        body {
            background-image: url('img/dash_plants_dark.jpg'); /* Set the background image URL */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .container {
            margin-top: 20px;
        }
        .box {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #d6d8db;
            border-radius: 5px;
        }
        .container h5 {
            text-align: center;
        }
        .form-label {
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .file-upload {
            margin-top: 20px;
        }
        .box-container {
            display: flex;
            justify-content: space-between;
        }
        /* changes to the title next color and size */
    </style>
    
</head>
<body style="overflow-x: hidden;"> 
    <nav class="navbar bg-dark navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard.php">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" style="text-align:right" href="tournaments.php">Tournaments</a>
                        <a class="nav-link" style="text-align:right" href="stats.php">Stats</a>
                        <a class="nav-link" style="text-align:right" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container"> 
    <h5 style="color: white; font-size: 32px;">Add and Remove Users</h5> 
        <div class="box-container">
            <div class="box" id="users">
                <form method="post">
                    <div class="form-group">
                        <label for="api_key" class="form-label">Enter RIOT API key to verify user ranks (or verify rank manually):</label>
                        <input type="text" id="api_key" name="api_key" class="form-control" value="Optional">
                    </div>
                    <div class="form-group">
                        <label for="user_name" class="form-label">Enter User Name:</label>
                        <input type="text" id="user_name" name="user_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="role" class="form-label">Enter Preferred Role:</label>
                        <input type="text" id="role" name="role" class="form-control" required>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </form>
            </div>

            <div class="box" id="usernames">
                <form method="post" enctype="multipart/form-data">
                    <label>User List:</label>
                    <select class="form-select" style="height:200px;" name="selected_users[]" multiple>
                        <?php
                            $result = $conn->query("SELECT name FROM players");

                            if ($result) {
                                $usernames = array();
                        
                                // Fetch usernames and add them to the session array
                                while ($row = $result->fetch_assoc()) {
                                    $usernames[] = $row['name'];
                                }
                        
                                $_SESSION['user_list'] = $usernames;
                            } else {
                                echo "Error querying the database: " . $conn->error;
                            }
                            // Close the database connection
                            $conn->close();
                            // Display the added users from the session array
                            foreach ($_SESSION['user_list'] as $user) {
                                echo "<option value='$user'>$user</option>";
                            }
                        ?>
                    </select>

                    <!-- TEST -->
                    <label for="user_file" class="form-label">Upload Spreadsheet:</label>
                    <input type="file" id="user_file" name="user_file" accept=".csv, .xls, .xlsx">
                    <button type="submit" name="upload_file" class="btn btn-success mt-3">Submit Spreadsheet</button>
                    <input type="hidden" id="api_key_copy" name="api_key" class="form-control" value="Optional">                    
                    <button type="submit" name="remove_selected" class="btn btn-danger mt-3">Remove Selected Users</button>
                    <!-- TEST -->
                </form>
                <!-- <form method="post" enctype="multipart/form-data" class="file-upload">
                    <label for="user_file" class="form-label">Upload Spreadsheet:</label>
                    <input type="file" id="user_file" name="user_file" accept=".csv, .xls, .xlsx">
                    <input type="text" id="api_key" name="api_key" class="form-control" value="Optional">
                    <button type="submit" name="upload_file" class="btn btn-success mt-3">Upload Spreadsheet</button>
                </form> -->
            </div>
            <script>
                // JavaScript to copy the api_key value from the first form to the hidden input in the second form
                document.getElementById('api_key').addEventListener('input', function() {
                    document.getElementById('api_key_copy').value = this.value;
                });
            </script>
        </div>
    </div>
</body>
</html>
