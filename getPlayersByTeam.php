<?php
include './db_config.php';

// Get team ID from the query parameters
$team_id = isset($_GET['team_id']) ? $_GET['team_id'] : null;
$team_id = htmlspecialchars(strip_tags(urldecode($team_id)));

if ($team_id !== null) {
    // Fetch players for the given team ID
    $sql = "SELECT `name`, `role_preferred` FROM players WHERE team_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('s', $team_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $players = $result->fetch_all(MYSQLI_ASSOC);

        // Send the player data as JSON response
        header('Content-Type: application/json');
        echo json_encode($players);
        $stmt->close();
    } else {
        // Handle the error
        die("Error: " . $conn->error);
    }
} else {
    // Invalid or missing team ID
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid or missing team ID']);
}
?>
