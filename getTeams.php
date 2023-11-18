<?php
include './db_config.php';

// Fetch team data from the database
$sql = "SELECT team_id FROM teams";
$result = $conn->query($sql);

$teams = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row;
    }
} else {
    // Handle the error
    die("Error: " . $conn->error);
}

// Send the team data as JSON response
header('Content-Type: application/json');
echo json_encode($teams);
?>
