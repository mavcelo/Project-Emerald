<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
function getSummonerRank($summonerName, $apiKey) {
    // API endpoint for getting a summoner's ID
    $summonerUrl = "https://na1.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . rawurlencode(htmlspecialchars(strip_tags($summonerName))) . "?api_key=" . $apiKey;

    // Send a request to get the summoner's ID
    $response = file_get_contents($summonerUrl);

    if ($response) {
        $summonerData = json_decode($response, true);

        if (isset($summonerData['id'])) {
            // Get the summoner ID
            $summonerId = $summonerData['id'];

            // API endpoint for getting summoner's rank
            $rankUrl = "https://na1.api.riotgames.com/lol/league/v4/entries/by-summoner/$summonerId?api_key=$apiKey";

            // Send a request to get summoner's rank
            $rankData = file_get_contents($rankUrl);

            if ($rankData) {
                $rankInfo = json_decode($rankData, true);

                // Check if the summoner has ranked data
                if (!empty($rankInfo)) {
                    // Find the "RANKED_SOLO_5x5" entry
                    foreach ($rankInfo as $entry) {
                        if ($entry['queueType'] === "RANKED_SOLO_5x5") {
                            $tier = $entry['tier'];
                            $rank = $entry['rank'];
                            return $tier . ' ' . $rank;
                        }
                    }
                    return "Error summoner $summonerName is unranked in RANKED_SOLO_5x5.";
                } else {
                    return "Error summoner $summonerName is unranked.";
                }
            } else {
                return "Error fetching rank data.";
            }
        } else {
            return "Error summoner not found.";
        }
    } else {
        return "Error fetching summoner data.";
    }
}


function getMatchData($matchId, $riotToken) {
    $url = "https://americas.api.riotgames.com/lol/match/v5/matches/$matchId";
    $headers = [
        "Accept: Application/json",
        "X-Riot-Token: $riotToken",
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($ch);

    if ($output === false) {
        // cURL request failed
        return ["error" => "cURL request failed"];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        // The API request returned an error response
        return ["error" => "API request error (HTTP code: $httpCode)"];
    }

    $decodedOutput = json_decode($output, true, JSON_PRETTY_PRINT);

    if ($decodedOutput === null) {
        // JSON decoding failed
        return ["error" => "Invalid JSON response"];
    }

    return $decodedOutput;
}

function getPlayerNamesFromMatch($matchId, $riotToken, $conn) {
    $matchData = getMatchData($matchId, $riotToken);
    if (isset($matchData['metadata']['participants'])) {
        $puuids = $matchData['metadata']['participants'];
        $playerNames = array();

        // Check the database for existing PUUIDs
        $existingPuuids = getExistingPuuidsFromDatabase($conn);
        $test = 0;
        foreach ($puuids as $puuid) {
            if (in_array($puuid, $existingPuuids)) {
                // PUUID exists in the database
                $playerName = getPlayerNameFromDatabase($conn, $puuid);
                $playerNames[] = $playerName;
            } else {
                // PUUID not in the database, retrieve the name from Riot API
                $playerData = getPlayerDataFromPuuid($puuid, $riotToken);
                if (isset($playerData['name'])) {
                    $playerName = $playerData['name'];
                    $playerNames[] = $playerName;

                    // Insert the PUUID and player name into the database
                    storePlayerDataInDatabase($conn, $puuid, $playerName);
                }
            }
            $test++;
        }

        return $playerNames;
    } else {
        return [];
    }
}




// Function to get PUUID from the database
function getExistingPuuidsFromDatabase($conn) {
    $existingPuuids = array();

    // Replace 'your_table_name' with the actual table name where PUUIDs are stored
    $tableName = 'players';

    $sql = "SELECT puuid FROM $tableName";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $existingPuuids[] = $row['puuid'];
        }
    }

    return $existingPuuids;
}



// Function to get player name from the database
function getPlayerNameFromDatabase($conn, $puuid) {
    // Assuming you have a table named "players" with columns "puuid" and "name" in your database

    $query = "SELECT name FROM players WHERE puuid = ?";
    
    // Prepare the query
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        // Bind the parameter
        $stmt->bind_param("s", $puuid);

        // Execute the query
        $stmt->execute();

        // Assign the player name variable
        $playerName = null;

        // Bind the result variable
        $stmt->bind_result($playerName);

        // Fetch the result
        $stmt->fetch();

        // Close the statement
        $stmt->close();

        if (!empty($playerName)) {
            return $playerName;
        }
    }

    return false; // Return false if the player name is not found in the database
}


function getPlayerDataFromPuuid($puuid, $riotToken) {
    $url = "https://na1.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/$puuid";
    $headers = [
        "Accept: Application/json",
        "X-Riot-Token: $riotToken",
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($ch);

    if ($output === false) {
        // cURL request failed
        return ["error" => "cURL request failed"];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        // The API request returned an error response
        return ["error" => "API request error (HTTP code: $httpCode)"];
    }

    $decodedOutput = json_decode($output, true);
    if ($decodedOutput === null) {
        // JSON decoding failed
        return ["error" => "Invalid JSON response"];
    }

    return $decodedOutput;
}

function storePlayerDataInDatabase($conn, $puuid, $playerName) {
    // You should replace 'your_table_name' with the actual table name in your database.
    $tableName = 'players';

    // Check if the name already exists in the database.
    $sql = "SELECT * FROM $tableName WHERE name = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $playerName);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the PUUID already exists, update the associated player name.
    if ($result->num_rows > 0) {
        $updateSql = "UPDATE $tableName SET puuid = ? WHERE name = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ss", $puuid, $playerName);
        $updateStmt->execute();
    }
}






// need to know current rank, last season peak rank, account level, games played this season/split