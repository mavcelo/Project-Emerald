<?php

// php spreadsheet library for speadsheet processing
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();

if(!isset($_SESSION['id'])) {
    header("Location: /index.php");
}

// check if user is admin
if($_SESSION['isadmin'] == TRUE) {
    $guestoradmin = 'Admin';
} else {
    $guestoradmin = 'Guest';
}

// Check if the user list array exists in the session, if not, create it
if (!isset($_SESSION['user_list'])) {
    $_SESSION['user_list'] = array();
}

// Check if the form is submitted to add a user
if (isset($_POST['add_user'])) {
    // Get the user's name from the form, and sanitize
    $newUser = htmlspecialchars(strip_tags($_POST['user_name']));

    // Add the new user to the session array
    $_SESSION['user_list'][] = $newUser;
}

// Check if the form is submitted to remove selected users
if (isset($_POST['remove_selected'])) {
    // Get the selected users to remove
    if (isset($_POST['selected_users'])) {
        $selectedUsers = $_POST['selected_users'];
        foreach ($selectedUsers as $user) {
            $index = array_search(htmlspecialchars(strip_tags($user)), $_SESSION['user_list']);
            if ($index !== false) {
                unset($_SESSION['user_list'][$index]);
            }
        }
    }

    // Loop through the selected users and remove them
        

    // Reset array keys to ensure it's sequential
    $_SESSION['user_list'] = array_values($_SESSION['user_list']);
}

if (isset($_FILES['user_file']) && $_FILES['user_file']['error'] === UPLOAD_ERR_OK) {
    // Get the uploaded file
    $file = $_FILES['user_file']['tmp_name'];

    // Load the spreadsheet using PhpSpreadsheet
    $spreadsheet = IOFactory::load($file);

    // Get the first worksheet
    $worksheet = $spreadsheet->getActiveSheet();

    // Iterate through the rows and add usernames to the session array
    foreach ($worksheet->getRowIterator() as $row) {
        $cellValue = $row->getCellIterator()->current()->getValue();
        if (!empty($cellValue)) {
            $_SESSION['user_list'][] = htmlspecialchars($cellValue);
        }
    }
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
                        <a class="nav-link" style="text-align:right" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="ms-4 mt-3 w3-border w3-round ws-grey col-md-4" id="users">
        <h5>Add and Remove Users</h5>

        <form method="post">
            <label for="user_name">Enter User Name:</label>
            <input type="text" id="user_name" name="user_name" required>
            <input type="submit" name="add_user" value="Add User">
        </form>
    </div><br>
    
    <div class="ms-4 mt-3 w3-border w3-round ws-grey col-md-4" id="usernames">
        <form method="post" enctype="multipart/form-data">
            <label>User List:</label>
            <select class="col-md-3 form-select" style="height:150px" name="selected_users[]" multiple>
                <?php
                // Display the added users from the session array
                foreach ($_SESSION['user_list'] as $user) {
                    echo "<option value='$user'>$user</option>";
                }
                ?>
            </select>
            <input type="submit" name="remove_selected" value="Remove Selected Users">
        </form>
    </div>

    <form method="post" enctype="multipart/form-data">
        <label for="user_file">Upload Spreadsheet:</label>
        <input type="file" id="user_file" name="user_file" accept=".csv, .xls, .xlsx">
        <input type="submit" name="upload_file" value="Upload Spreadsheet">
    </form>

</body>
</html>
