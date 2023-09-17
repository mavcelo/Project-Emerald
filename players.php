<?php
// Start or resume the session
session_start();

// Check if the user list array exists in the session, if not, create it
if (!isset($_SESSION['user_list'])) {
    $_SESSION['user_list'] = array();
}

// Check if the form is submitted to add a user
if (isset($_POST['add_user'])) {
    // Get the user's name from the form
    $newUser = $_POST['user_name'];

    // Add the new user to the session array
    $_SESSION['user_list'][] = $newUser;
}

// Check if the form is submitted to remove selected users
if (isset($_POST['remove_selected'])) {
    // Get the selected users to remove
    $selectedUsers = $_POST['selected_users'];

    // Loop through the selected users and remove them
    foreach ($selectedUsers as $user) {
        $index = array_search($user, $_SESSION['user_list']);
        if ($index !== false) {
            unset($_SESSION['user_list'][$index]);
        }
    }

    // Reset array keys to ensure it's sequential
    $_SESSION['user_list'] = array_values($_SESSION['user_list']);
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
<body>
    <div class="ms-4 mt-3 w3-border w3-round ws-grey col-md-4" id="users">
        <h5>Add and Remove Users</h5>

        <form method="post">
            <label for="user_name">Enter User Name:</label>
            <input type="text" id="user_name" name="user_name" required>
            <input type="submit" name="add_user" value="Add User">
        </form>
    </div><br>
    
    <div class="ms-4 mt-3 w3-border w3-round ws-grey col-md-4" id="usernames">
        <form method="post">
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
</body>
</html>
