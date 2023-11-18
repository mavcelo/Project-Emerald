<?php
include './db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playerName = $_POST['playerName'];
    $teamName = $_POST['teamName'];

    // Update the player's team_id in the players table
    $sql = "UPDATE players SET team_id = ? WHERE name = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('ss', $teamName, $playerName);
        $result = $stmt->execute();

        if ($result) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'error' => $stmt->error];
        }

        $stmt->close();
    } else {
        $response = ['success' => false, 'error' => $conn->error];
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle other request methods if needed
    http_response_code(405); // Method Not Allowed
}
?>
