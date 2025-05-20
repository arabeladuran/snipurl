<?php
    $mysqli = require __DIR__ . "/database.php";

    $short_url = $_GET["c"] ?? '';

    if(empty($short_url)) {
        http_response_code(404);
    }

    // check databse
    $query = "SELECT long_url FROM links WHERE short_url = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $short_url);
    $stmt->execute();
    $result = $stmt->get_result();

    // get results
    if ($row = $result->fetch_assoc()) {
        header("Location: " . $row['long_url']);
        exit;
    }
?>