<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location:signup.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$query = "SELECT * FROM links WHERE user_id = ? ORDER BY created_at DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// get results
$result = $stmt->get_result();
$links = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link History</title>
    <link rel="stylesheet" href="styles/global.css">
</head>

<body>
    <nav>

    </nav>

    <main>
        <div class="container">
            <div>
                <h1>Your links</h1>
                <p>View and edit your links here</p>
                <form>
                    <input type="text" placeholder="Search">
                    <button class="btn" id="form-btn">SORT</button>
                </form>
                <button class="btn">Create Link</button>
            </div>

            <div class="link-list">
                <?php if (count($links) === 0): ?>
                    <h2>No links yet</h2>
                <?php else: ?>
                    <?php foreach ($links as $link): ?>

                        <div>
                            <h2>Link Title</h2>
                            <a href="">Edit</a>
                            <a href="">Delete</a>
                        </div>

                        <div>
                            <a href="<?= htmlspecialchars('http://localhost/SnipURL/' . $link['short_url']) ?>" target="_blank">
                                localhost/SnipURL/<?= htmlspecialchars($link['short_url']) ?>
                            </a>

                            <p><?= htmlspecialchars($link['long_url']) ?></p>
                        </div>

                        <div>
                            <button class="btn">Copy</button>
                            <p>Created on <?= htmlspecialchars($link['created_at']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </main>
</body>

</html>