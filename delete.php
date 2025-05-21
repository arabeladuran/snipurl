<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location:index.php");
    exit;
}

$short_url = $_POST["short_url"] ?? "";

// check databse
$query = "DELETE FROM links WHERE short_url = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $short_url);
$stmt->execute();

// get results
if ($stmt->affected_rows > 0) {
    header("Location: links.php"); // Redirect back to history
    exit;
} else {
    echo "Failed to delete link or unauthorized.";
}
