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
        $role = htmlspecialchars(strip_tags($_POST['role']));
    }
    // Insert the new user into the database
    $sql = "INSERT INTO players (name, `rank`, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $username, $rank, $role);

        try {
            $stmt->execute();
            $_SESSION['user_list'][] = $username;

        } catch (Exception $e) {
            echo "<span style='color:red'>Character Length too long</span>";
        }
        
        $stmt->close();
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





// Iterate through the rows
if (isset($_FILES['user_file']) && $_FILES['user_file']['error'] === UPLOAD_ERR_OK) {
    // Get the uploaded file
    $file = $_FILES['user_file']['tmp_name'];

    // Load the spreadsheet using PhpSpreadsheet
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

    // Get the first worksheet
    $worksheet = $spreadsheet->getActiveSheet();

    // Iterate through the rows
    foreach ($worksheet->getRowIterator() as $row) {
        // Get the values for name, rank, and role from the respective columns
        $name = $worksheet->getCell(1, $row->getRowIndex())->getValue();
        $rank = $worksheet->getCell(2, $row->getRowIndex())->getValue();
        $role = $worksheet->getCell(3, $row->getRowIndex())->getValue();

        // Skip rows with empty name, rank, or role
        if (!empty($name) && !empty($rank) && !empty($role)) {
            // Insert data into the database using prepared statements
            $sql = "INSERT INTO players (name, `rank`, role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $nameValue = htmlspecialchars($name);
            $rankValue = htmlspecialchars($rank);
            $roleValue = htmlspecialchars($role);
            
            
            if ($stmt) {
                $stmt->bind_param("sss", $nameValue, $rankValue, $roleValue);   

                if ($stmt->execute()) {
                    relax();
                } else {
                    echo "Error executing SQL statement: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing SQL statement: " . $conn->error;
            }
        }
        
        
    }
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
}
    




?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./style.css">
    <title>Add and Remove Users</title>
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
                        <a class="nav-link" style="text-align:right" href="stats.php">Stats</a>
                        <a class="nav-link" style="text-align:right" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container align-items-center justify-content-center col-md-4" id="users">
        <h5>Add and Remove Users</h5>

        <form method="post">
            <label for="api_key">Enter RIOT API key (or verify rank manually):</label>
            <input type="text" id="api_key" name="api_key" value="Optional">
            <label for="user_name">Enter User Name:</label>
            <input type="text" id="user_name" name="user_name" required><br>
            <label for="role">Enter Preferred Role:</label>
            <input type="text" id="role" name="role" required>
            <input type="submit" name="add_user" value="Add User">
        </form>
    </div><br>
    
    <div class="container col-md-4" id="usernames">
        <form method="post" enctype="multipart/form-data">
            <label>User List:</label>
            <select class="col-md-3 form-select" style="height:500px;width:400px;" name="selected_users[]" multiple>
                <?php
                // Display the added users from the session array
                foreach ($_SESSION['user_list'] as $user) {
                    echo "<option value='$user'>$user</option>";
                }
                ?>
            </select>
            <input type="submit" name="remove_selected" value="Remove Selected Users">
        </form>
        <form method="post" enctype="multipart/form-data" style="padding-top:10px">
            <label for="user_file">Upload Spreadsheet:</label>
            <input type="file" id="user_file" name="user_file" accept=".csv, .xls, .xlsx">
            <input type="submit" name="upload_file" value="Upload Spreadsheet">
        </form>
    </div>



</body>
</html>