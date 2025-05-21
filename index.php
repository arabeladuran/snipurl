<?php session_start();

if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__ . "/database.php";

    // get user info
    $query = "SELECT * FROM users WHERE id = {$_SESSION["user_id"]}";

    // get results
    $result = $mysqli->query($query);
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <link href="styles/global.css" rel="stylesheet">
</head>

<body>

    <nav>
        <a href="index.php" class="nav-logo">SHORTURL</a>

        <div class="nav-btn">
            <?php
            if (isset($user)): ?>
                <a href="logout.php" class="btn" id="signup-btn">Log out</a>
            <?php else: ?>
                <!-- If user is not logged in, redirect to sign up page -->
                <a href="login.php" class="btn" id="login-btn">Log in</a>
                <a href="signup.php" class="btn" id="signup-btn">Start for Free</a>
            <?php endif; ?>

        </div>
    </nav>

    <main>
        <div class="container" id="shortenlink-cnt">
            <h1>Paste your link here</h1>
            <input class="input"type="text"><br>

            <?php if (!isset($user)): ?>
                <!-- If user is not logged in, redirect to sign up page -->
                <a href="signup.php" class="btn" style="color:black">Shorten Link</a>
            <?php endif; ?>

        </div>
    </main>
</body>

</html>