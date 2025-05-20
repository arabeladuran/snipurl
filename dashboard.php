<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/database.php";

    $long_url = $_POST["long-url"];
    $short_url = $_POST["short-url"];

    $query = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/global.css">
</head>

<body>
    <main>
        <div class="container">
            <h1>Home</h1>
            <form method="post">
                <!-- Link to shorten -->
                <h2>Quick Create</h2>
                <div>
                    <label for="long-url">Enter long URL</label>
                    <input type="text" id="long-url" name="long-url" placeholder="e.g. https://example.com/longlink">
                </div>
                <div>
                    <label for="title">Title (Optional)</label>
                    <input type="text" id="title" name="title">
                </div>
                <div>
                    <label for="short-url">Custom URL</label>
                    <input type="text" value="snip-url.com/" readonly>
                    <input type="text" id="short-url" name="short-url">
                </div>

                <button class="btn" id="form-btn">Snip your link</button>
            </form>
        </div>

    </main>
</body>

</html>