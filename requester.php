<?php
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

    $decodedOutput = json_decode($output, true);

    if ($decodedOutput === null) {
        // JSON decoding failed
        return ["error" => "Invalid JSON response"];
    }

    return $decodedOutput;
}


// need to know current rank, last season peak rank, account level, games played this season/split